<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user_id']) || !isset($_SESSION['email']) || $_SESSION['email'] !== 'admin@learnify.com') {
    header("Location: ../index.php"); 
    exit();
}

include '../includes/config.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("<div style='background-color:#0f172a; color:#ef4444; text-align:center; padding:100px; font-weight:bold; font-family:sans-serif;'>ADMIN ACCESS REQUIRED!</div>");
}


if (isset($_POST['add_category'])) {
    $cat_name = trim($_POST['cat_name']);
    $slug = strtolower(str_replace(' ', '-', $cat_name));
    $stmt = $conn->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
    $stmt->bind_param("ss", $cat_name, $slug);
    $stmt->execute();
    header("Location: dashboard.php");
    exit();
}


if (isset($_POST['admin_upload'])) {
    $title = trim($_POST['title']);
    $category_id = intval($_POST['category_id']);
    $price = floatval($_POST['price']);
    $is_paid = $price > 0 ? 1 : 0;
    $user_id = $_SESSION['user_id']; // admin

    
    $upload_dir = '../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $thumbnail_path = 'uploads/default_thumb.jpg';

    
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['thumbnail']['tmp_name'];
        $file_name = time() . '_thumb_' . basename($_FILES['thumbnail']['name']);
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($file_tmp, $target_file)) {
         
            $thumbnail_path = 'uploads/' . $file_name;
        }
    }

    
    $video_source = isset($_POST['video_source']) ? $_POST['video_source'] : 'url';
    $video_url = '';

    if ($video_source === 'file') {
        if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
            $video_tmp = $_FILES['video_file']['tmp_name'];
            $video_name = time() . '_video_' . basename($_FILES['video_file']['name']);
            $target_video = $upload_dir . $video_name;
            
            if (move_uploaded_file($video_tmp, $target_video)) {
                $video_url = 'uploads/' . $video_name;
            }
        }
    } else {
        $video_url = trim($_POST['url']);
        // Format YouTube links into Embed URLs if applicable
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $video_url, $match)) {
            $video_url = "https://www.youtube.com/embed/" . $match[1];
        }
    }

    
    if (!empty($video_url)) {
        $stmt = $conn->prepare("INSERT INTO courses (title, url, thumbnail, category_id, user_id, price, is_paid, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'approved')");
        $stmt->bind_param("sssiidi", $title, $video_url, $thumbnail_path, $category_id, $user_id, $price, $is_paid);
        $stmt->execute();
    }
    
    header("Location: dashboard.php");
    exit();
}

// 3. APPROVE CREATOR STREAM
if (isset($_GET['approve'])) {
    $approve_id = intval($_GET['approve']);
    $conn->query("UPDATE courses SET status='approved' WHERE id=$approve_id");
    header("Location: dashboard.php");
    exit();
}

// Fetch general dashboard statistics
$admin_id = $_SESSION['user_id'];
$admin_usr = $conn->query("SELECT wallet_balance FROM users WHERE id=$admin_id")->fetch_assoc();
$total_pending = $conn->query("SELECT COUNT(*) as cnt FROM courses WHERE status='pending'")->fetch_assoc()['cnt'];
$total_categories = $conn->query("SELECT COUNT(*) as cnt FROM categories")->fetch_assoc()['cnt'];

// Pull Queues
$pending_videos = $conn->query("SELECT c.*, u.username as creator, cat.name as category_name FROM courses c 
                                JOIN users u ON c.user_id = u.id 
                                JOIN categories cat ON c.category_id = cat.id 
                                WHERE c.status='pending'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SH_Learnify V3 - Enterprise Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#0b0f19] text-gray-100 font-sans">

    <header class="bg-gray-900 border-b border-gray-800 px-8 py-4 flex items-center justify-between">
        <a href="../index.php" class="text-xl font-black text-red-500"><i class="fa fa-arrow-left"></i> SH_LEARNIFY <span class="text-white">PORTAL</span></a>
        <div class="flex items-center space-x-6">
            <span class="text-sm font-semibold text-green-400"><i class="fa fa-wallet"></i> Admin Wallet Balance: <?php echo number_format($admin_usr['wallet_balance'] ?? 0, 2) . ' BDT'; ?></span>
            <a href="../logout.php" class="text-xs font-semibold text-red-400 hover:text-red-300">SYSTEM LOGOUT</a>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-8 py-12">
        <h1 class="text-4xl font-black mb-1 text-white tracking-tight">ENTERPRISE ADMIN HUB</h1>
        <p class="text-gray-400 text-sm mb-10">Configure categories, approve digital streams and post premium courses.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-gray-900 border border-gray-800 p-6 rounded-2xl shadow-xl">
                <span class="text-gray-400 text-xs font-bold uppercase block mb-1">Company Wallet</span>
                <span class="text-3xl font-black text-green-400"><?php echo number_format($admin_usr['wallet_balance'] ?? 0, 2) . ' ৳'; ?></span>
            </div>
            <div class="bg-gray-900 border border-gray-800 p-6 rounded-2xl shadow-xl">
                <span class="text-gray-400 text-xs font-bold uppercase block mb-1">Pending Approvals</span>
                <span class="text-3xl font-black text-yellow-500"><?php echo $total_pending; ?> Videos</span>
            </div>
            <div class="bg-gray-900 border border-gray-800 p-6 rounded-2xl shadow-xl">
                <span class="text-gray-400 text-xs font-bold uppercase block mb-1">System Sectors</span>
                <span class="text-3xl font-black text-indigo-400"><?php echo $total_categories; ?> Categories</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-8">
                
                <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-xl">
                    <h3 class="text-lg font-bold mb-4 text-white"><i class="fa fa-upload text-indigo-500 mr-2"></i>Publish Premium / Paid Course</h3>
                    <form method="POST" enctype="multipart/form-data" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-400 uppercase font-bold mb-2">Title</label>
                                <input type="text" name="title" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 uppercase font-bold mb-2">Video Resource Type</label>
                                <select name="video_source" id="video_source" onchange="toggleVideoSource(this.value)" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none">
                                    <option value="url">External Link / YouTube URL</option>
                                    <option value="file">Local MP4 File Upload</option>
                                </select>
                            </div>
                        </div>

                        <div id="video_url_input" class="block">
                            <label class="block text-xs text-gray-400 uppercase font-bold mb-2">Streaming Resource Link (YouTube or Direct MP4 URL)</label>
                            <input type="url" name="url" placeholder="https://www.youtube.com/watch?v=..." class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none">
                        </div>

                        <div id="video_file_input" class="hidden">
                            <label class="block text-xs text-gray-400 uppercase font-bold mb-2">Select Local Video File (.mp4)</label>
                            <input type="file" name="video_file" accept="video/mp4" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-gray-300 focus:outline-none file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-gray-700 file:text-white hover:file:bg-gray-600">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-400 uppercase font-bold mb-2">Video Thumbnail Cover Image (.jpg / .png)</label>
                                <input type="file" name="thumbnail" accept="image/*" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-gray-300 focus:outline-none file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-gray-700 file:text-white hover:file:bg-gray-600">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 uppercase font-bold mb-2">Price (BDT ৳) [0 = Free]</label>
                                <input type="number" step="0.01" name="price" value="4500.00" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-400 uppercase font-bold mb-2">Course Category</label>
                            <select name="category_id" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none">
                                <?php 
                                $cats = $conn->query("SELECT * FROM categories");
                                while($c = $cats->fetch_assoc()): ?>
                                    <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <button type="submit" name="admin_upload" class="w-full bg-indigo-600 hover:bg-indigo-700 py-3 rounded-lg font-bold transition">Publish Premium Content</button>
                    </form>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-xl">
                    <h3 class="text-lg font-bold mb-4 text-white"><i class="fa fa-stamp text-yellow-500 mr-2"></i>Content Moderation Queue</h3>
                    
                    <?php if($pending_videos && $pending_videos->num_rows > 0): ?>
                        <div class="space-y-4">
                            <?php while($pv = $pending_videos->fetch_assoc()): ?>
                                <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-4 flex items-center justify-between">
                                    <div>
                                        <h4 class="font-bold text-white"><?php echo htmlspecialchars($pv['title']); ?></h4>
                                        <p class="text-xs text-gray-400 mt-1">Creator: <strong><?php echo htmlspecialchars($pv['creator']); ?></strong> | Category: <strong><?php echo htmlspecialchars($pv['category_name']); ?></strong></p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <a href="dashboard.php?approve=<?php echo $pv['id']; ?>" class="bg-green-600 hover:bg-green-700 text-white font-bold text-xs px-4 py-2 rounded-lg transition"><i class="fa fa-check"></i> Approve</a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-400 text-center py-6 text-sm">No pending moderation videos inside queue.</p>
                    <?php endif; ?>
                </div>

            </div>

            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-xl h-fit">
                <h3 class="text-lg font-bold mb-4 text-white"><i class="fa fa-tags text-red-500 mr-2"></i>Create Custom Category</h3>
                <form method="POST" class="space-y-4 mb-6">
                    <div>
                        <label class="block text-xs text-gray-400 uppercase font-bold mb-2">Category Name</label>
                        <input type="text" name="cat_name" required placeholder="e.g. Cooking Mastery" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none">
                    </div>
                    <button type="submit" name="add_category" class="w-full bg-red-600 hover:bg-red-700 py-3 rounded-lg font-bold transition">Generate Category</button>
                </form>

                <h4 class="text-xs text-gray-400 uppercase font-bold mb-3">Existing Platform Categories</h4>
                <div class="space-y-2">
                    <?php 
                    $cats = $conn->query("SELECT * FROM categories");
                    while($c = $cats->fetch_assoc()): ?>
                        <div class="bg-gray-800/40 px-4 py-2.5 rounded-lg border border-gray-800 flex justify-between text-sm">
                            <span class="text-white"><?php echo htmlspecialchars($c['name']); ?></span>
                            <span class="text-xs text-gray-500">slug: <?php echo htmlspecialchars($c['slug']); ?></span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

        </div>
    </div>

    <script>
        function toggleVideoSource(val) {
            if (val === 'file') {
                document.getElementById('video_file_input').classList.replace('hidden', 'block');
                document.getElementById('video_url_input').classList.replace('block', 'hidden');
            } else {
                document.getElementById('video_file_input').classList.replace('block', 'hidden');
                document.getElementById('video_url_input').classList.replace('hidden', 'block');
            }
        }
    </script>
</body> 
</html>
