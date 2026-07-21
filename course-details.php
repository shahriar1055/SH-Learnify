<?php
include 'includes/header.php';
$id = intval($_GET['id'] ?? 0);
$res = $conn->query("SELECT c.*, cat.name as category_name, u.username as author_name FROM courses c 
                     JOIN categories cat ON c.category_id=cat.id 
                     JOIN users u ON c.user_id=u.id 
                     WHERE c.id=$id");
$course = $res->fetch_assoc();

if(!$course) {
    die("<div class='text-center py-20 text-red-500 font-bold'>Course or Video Not Found!</div>");
}
?>
<div class="max-w-4xl mx-auto px-6 py-12">
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-2xl">
        <video src="<?php echo $course['url']; ?>" controls class="w-full aspect-video rounded-lg mb-6 bg-black"></video>
        
        <div class="flex items-center justify-between mb-4">
            <span class="px-3 py-1 bg-red-600/20 text-red-400 font-bold uppercase text-xs rounded"><?php echo $course['category_name']; ?></span>
            <span class="text-xs text-gray-400">Uploaded by: <strong><?php echo $course['author_name']; ?></strong></span>
        </div>
        
        <h1 class="text-3xl font-black text-white mb-3"><?php echo $course['title']; ?></h1>
        <p class="text-gray-300 leading-relaxed"><?php echo $course['description']; ?></p>
        
        <div class="mt-8 pt-6 border-t border-gray-700 flex items-center justify-between">
            <div>
                <span class="text-sm text-gray-400 block">Access Price</span>
                <span class="text-2xl font-black text-green-400"><?php echo $course['is_paid'] ? CURRENCY.' '.number_format($course['price'],2) : 'FREE VIDEO'; ?></span>
            </div>
            
            <?php if($course['is_paid']): ?>
                <a href="dashboard.php?view=cart&add=<?php echo $course['id']; ?>" class="bg-indigo-600 hover:bg-indigo-700 px-6 py-3 rounded-lg font-bold text-white transition"><i class="fa fa-cart-plus mr-2"></i> Enroll Now</a>
            <?php else: ?>
                <span class="text-green-400 bg-green-500/10 px-4 py-2 rounded-lg border border-green-500/20 font-bold">Unrestricted Access</span>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
