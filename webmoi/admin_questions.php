<?php
// Kiểm tra và lấy dữ liệu câu hỏi
$sql = "SELECT pq.*, p.name as product_name 
        FROM product_questions pq
        LEFT JOIN product p ON pq.product_id = p.id
        ORDER BY pq.created_at DESC";

$result = mysqli_query($conn, $sql);
$questions = [];
if ($result) {
    $questions = $result->fetch_all(MYSQLI_ASSOC);
}

// Xử lý trả lời câu hỏi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_answer'])) {
    $question_id = intval($_POST['question_id']);
    $answer = mysqli_real_escape_string($conn, $_POST['answer']);
    
    $stmt = $conn->prepare("UPDATE product_questions SET answer = ?, answered_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $answer, $question_id);
    $stmt->execute();
    $stmt->close();


}

// Xử lý xóa câu hỏi
if (isset($_GET['delete'])) {
    $question_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM product_questions WHERE id = ?");
    $stmt->bind_param("i", $question_id);
    $stmt->execute();
    $stmt->close();
    

}
?>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 glass-effect">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Quản Lý Câu Hỏi</h2>
        <div class="flex space-x-3">
            <div class="relative">
                <input type="text" id="search-questions" placeholder="Tìm kiếm..." 
                       class="pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="?page=questions&filter=all" 
                   class="px-3 py-1 rounded-lg <?php echo ($_GET['filter'] ?? 'all') === 'all' ? 'bg-primary-500 text-white' : 'bg-gray-200 dark:bg-gray-700'; ?>">
                    Tất cả
                </a>
                <a href="?page=questions&filter=answered" 
                   class="px-3 py-1 rounded-lg <?php echo ($_GET['filter'] ?? '') === 'answered' ? 'bg-primary-500 text-white' : 'bg-gray-200 dark:bg-gray-700'; ?>">
                    Đã trả lời
                </a>
                <a href="?page=questions&filter=unanswered" 
                   class="px-3 py-1 rounded-lg <?php echo ($_GET['filter'] ?? '') === 'unanswered' ? 'bg-primary-500 text-white' : 'bg-gray-200 dark:bg-gray-700'; ?>">
                    Chưa trả lời
                </a>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="mb-4 p-3 rounded-lg <?php echo $_GET['success'] == 1 ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'; ?>">
            <?php echo $_GET['success'] == 1 ? 'Đã cập nhật câu trả lời thành công!' : 'Đã xóa câu hỏi thành công!'; ?>
        </div>
    <?php endif; ?>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Sản Phẩm</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Người Hỏi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Câu Hỏi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ngày Hỏi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Trạng Thái</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Thao Tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                <?php if (empty($questions)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Không có câu hỏi nào</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($questions as $q): ?>
                        <?php 
                        // Áp dụng filter
                        $show = true;
                        if (isset($_GET['filter'])) {
                            if ($_GET['filter'] === 'answered' && empty($q['answer'])) {
                                $show = false;
                            } elseif ($_GET['filter'] === 'unanswered' && !empty($q['answer'])) {
                                $show = false;
                            }
                        }
                        
                        if ($show):
                        ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">#<?php echo $q['id']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo $q['product_name'] ? htmlspecialchars($q['product_name']) : 'Sản phẩm đã xóa'; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($q['user_name']); ?></td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($q['question']); ?></div>
                                <?php if ($q['answer']): ?>
                                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        <strong>Trả lời:</strong> <?php echo htmlspecialchars($q['answer']); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo date('d/m/Y H:i', strtotime($q['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full <?php echo $q['answer'] ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'; ?>">
                                    <?php echo $q['answer'] ? 'Đã trả lời' : 'Chờ trả lời'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="toggleAnswerForm(<?php echo $q['id']; ?>)" 
                                        class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 mr-3">
                                    <?php echo $q['answer'] ? 'Sửa' : 'Trả lời'; ?>
                                </button>
                                <a href="?page=questions&delete=<?php echo $q['id']; ?>" 
                                   class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                   onclick="return confirm('Bạn có chắc muốn xóa câu hỏi này?')">
                                    Xóa
                                </a>
                            </td>
                        </tr>
                        <tr id="answer-form-<?php echo $q['id']; ?>" class="hidden">
                            <td colspan="7" class="px-6 py-4 bg-gray-50 dark:bg-gray-800">
                                <form method="POST" action="?page=questions">
                                    <input type="hidden" name="question_id" value="<?php echo $q['id']; ?>">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Câu trả lời:</label>
                                        <textarea name="answer" rows="3" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600" required><?php echo htmlspecialchars($q['answer'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="flex justify-end space-x-3">
                                        <button type="button" onclick="toggleAnswerForm(<?php echo $q['id']; ?>)" 
                                                class="px-4 py-2 border rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            Hủy
                                        </button>
                                        <button type="submit" name="submit_answer" 
                                                class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600">
                                            Lưu Trả Lời
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleAnswerForm(questionId) {
    const form = document.getElementById(`answer-form-${questionId}`);
    form.classList.toggle('hidden');
    
    if (!form.classList.contains('hidden')) {
        form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

// Tìm kiếm câu hỏi
document.getElementById('search-questions').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (row.id.startsWith('answer-form-')) return;
        
        const questionText = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
        const productText = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        const userText = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        
        if (questionText.includes(searchTerm) || productText.includes(searchTerm) || userText.includes(searchTerm)) {
            row.style.display = '';
            // Hiển thị cả form trả lời nếu có
            const answerForm = document.getElementById(`answer-form-${row.querySelector('td:first-child').textContent.substring(1)}`);
            if (answerForm) answerForm.style.display = '';
        } else {
            row.style.display = 'none';
            // Ẩn cả form trả lời
            const answerForm = document.getElementById(`answer-form-${row.querySelector('td:first-child').textContent.substring(1)}`);
            if (answerForm) answerForm.style.display = 'none';
        }
    });
});
</script>