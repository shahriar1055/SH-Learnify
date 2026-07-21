<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'config.php';

//  admin@learnify.com 
$is_super_admin = false;
if (isset($_SESSION['user_id']) && isset($_SESSION['email']) && $_SESSION['email'] === 'admin@learnify.com') {
    $is_super_admin = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SH Learnify V3 - Premium Education Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#0b0f19] text-gray-100 font-sans antialiased selection:bg-blue-600 selection:text-white">

    <header class="bg-slate-100 border-b border-slate-200 sticky top-0 z-50 px-4 md:px-8 py-4 flex items-center justify-between shadow-md">
        <div class="flex items-center space-x-6">
            <a href="index.php" class="text-xl md:text-2xl font-black text-slate-900 tracking-wider">SH_<span class="text-blue-600">LEARNIFY</span><span class="text-[10px] bg-slate-900 text-white font-bold ml-1 px-1.5 py-0.5 rounded">V3.0</span></a>
            <nav class="hidden lg:flex space-x-6 text-sm font-bold">
                <a href="index.php?cat=academic" class="text-slate-700 hover:text-blue-600 transition flex items-center"><i class="fa fa-book mr-1.5 text-blue-500"></i> Academic Content</a>
                <a href="index.php?cat=skills" class="text-slate-700 hover:text-blue-600 transition flex items-center"><i class="fa fa-laptop-code mr-1.5 text-slate-700"></i> Professional Skills</a>
                <a href="courses.php" class="text-slate-700 hover:text-blue-600 transition flex items-center"><i class="fa fa-graduation-cap mr-1.5 text-emerald-600"></i> Premium Hub</a>
            </nav>
        </div>

        <div class="flex items-center space-x-4 md:space-x-6">
            <a href="dashboard.php?view=cart" class="relative text-slate-700 hover:text-blue-600 text-xl transition">
                <i class="fa fa-shopping-bag"></i>
                <span class="absolute -top-2 -right-2 bg-blue-600 text-white rounded-full text-[10px] px-1.5 font-bold shadow-sm">
                    <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                </span>
            </a>

            <?php if(isset($_SESSION['user_id'])): ?>
                <div class="hidden sm:flex flex-col text-right">
                    <span class="text-xs text-slate-600 font-medium">Learner: <strong class="text-slate-900"><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                    <span class="text-xs font-black text-slate-900 mt-0.5 flex items-center justify-end gap-1">
                        <i class="fa fa-wallet text-slate-500"></i> <?php echo number_format($_SESSION['wallet_balance'], 2) . ' BDT'; ?>
                    </span>
                </div>
                
                <a href="dashboard.php?view=recharge" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs px-4 py-2 rounded-xl font-bold transition flex items-center gap-1.5 shadow-sm">
                    <i class="fa fa-plus-circle text-white"></i> Add Money
                </a>

                <?php if($is_super_admin): ?>
                    <a href="admin/dashboard.php" class="bg-slate-950 hover:bg-black text-white text-xs md:text-sm px-4 py-2 rounded-xl font-bold shadow-md shadow-slate-900/10 transition">Admin Panel</a>
                <?php else: ?>
                    <a href="dashboard.php?view=enrolled" class="bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs md:text-sm px-4 py-2 rounded-xl font-bold shadow-sm transition">My Classroom</a>
                <?php endif; ?>
                
                <a href="logout.php" class="text-slate-500 hover:text-red-600 text-xs font-bold transition">Logout</a>
            <?php else: ?>
                <a href="login.php" class="text-slate-700 hover:text-slate-900 text-xs md:text-sm font-bold transition">Log In</a>
                <a href="register.php" class="bg-blue-600 hover:bg-blue-700 text-xs md:text-sm px-4 py-2 rounded-xl font-bold text-white shadow-md shadow-blue-500/10 transition">Sign Up</a>
            <?php endif; ?>
        </div>
    </header>
