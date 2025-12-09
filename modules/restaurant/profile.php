<?php
require_once '../../config/db.php';
session_start();

// AJAX POST request for profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax'])) {
    $user_id = $_SESSION["user_id"] ?? 0;
    $name = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $phone = trim(mysqli_real_escape_string($conn, $_POST['phone']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $response = [];

    if (!preg_match("/^\d{11}$/", $phone)) {
        $response['error'] = "Phone number must be exactly 11 digits and contain only numbers.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['error'] = "Please enter a valid email address.";
    } else {
        $update_sql = "UPDATE users SET name='$name', phone_number='$phone', email='$email' WHERE id=$user_id";
        if (mysqli_query($conn, $update_sql)) {
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $response['success'] = "Profile updated successfully!";
        } else {
            $response['error'] = "Database error: " . mysqli_error($conn);
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// --- Normal page load ---
include("Navbar.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch user info
$result = mysqli_query($conn, "SELECT name, phone_number, email FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($result);

// Fetch order history with item names
$history_sql = "
SELECT o.id AS order_id, o.created_at, i.name AS item_name, oi.quantity
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
JOIN items i ON oi.item_id = i.id
WHERE o.user_id = $user_id AND o.status = 1
ORDER BY o.created_at DESC
";
$history_result = mysqli_query($conn, $history_sql);

$order_rows = [];
while($row = mysqli_fetch_assoc($history_result)) {
    $order_rows[] = [
        'Order ID' => $row['order_id'],
        'Created At' => $row['created_at'],
        'Item Name' => $row['item_name'],
        'Quantity' => $row['quantity']
    ];
}

$headers = ['Order ID', 'Created At', 'Item Name', 'Quantity'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile & Order History</title>
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-black text-white">

<div class="h-screen flex flex-col justify-start items-center py-10 gap-10 w-full">

    <!-- Profile Form -->
    <form id="profileForm" class="card transition-all duration-300 text-white w-80 lg:w-[500px] h-96 bg-[#121618] border border-white/20 shadow-lg rounded-lg flex flex-col justify-center items-center gap-5">
        <h2 class="text-2xl lg:text-3xl transition-all duration-300">My Profile</h2>
        <div class="flex lg:gap-5 gap-3 lg:items-center lg:flex-row flex-col">
            <label for="username">Name</label>
            <input type="text" class="border px-2 border-white/20 rounded-lg w-72 py-1" name="username" value="<?php echo $user['name']; ?>" readonly>
        </div>
        <div class="flex lg:gap-5 gap-3 lg:items-center lg:flex-row flex-col">
            <label for="phone">Phone</label>
            <input type="text" class="border px-2 border-white/20 rounded-lg w-72 py-1" name="phone" value="<?php echo $user['phone_number']; ?>" readonly>
        </div>
        <div class="flex lg:gap-6 gap-3 lg:items-center lg:flex-row flex-col">
            <label for="email">Email</label>
            <input type="email" class="border px-2 border-white/20 rounded-lg w-72 py-1" name="email" value="<?php echo $user['email']; ?>" readonly>
        </div>
        <button type="button" id="editBtn" class="bg-[#fac564] lg:ms-16 w-72 rounded-lg py-1 cursor-pointer border text-black font-semibold border-white/20">Edit</button>
        <input type="hidden" name="ajax" value="1">
    </form>

    <!-- Order History Table -->
    <div class="overflow-x-auto rounded-lg shadow-md border border-white/20 w-full max-w-6xl transition-all duration-300">
        <table class="min-w-full divide-y max-h-[500px] ">
            <thead class="bg-[#121618] border border-white/20 shadow-lg ">
                <tr>
                    <?php foreach($headers as $col): ?>
                    <th class="lg:px-6 px-2 py-3 text-center text-[10px] lg:text-sm font-semibold uppercase tracking-wider transition-all duration-300"><?php echo $col; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php foreach($order_rows as $row): ?>
                <tr class="duration-150 hover:bg-[#2a2e31]">
                    <?php foreach($headers as $col): ?>
                    <td class="lg:px-6 px-2 py-3 text-center text-[10px] lg:text-sm transition-all duration-300"><?php echo $row[$col]; ?></td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-5 right-5 px-4 py-2 rounded-lg text-white shadow-lg opacity-0 pointer-events-none transition-all duration-500 z-50"></div>

<script>
const form = document.getElementById('profileForm');
const editBtn = document.getElementById('editBtn');
const inputs = form.querySelectorAll('input[name]:not([type=hidden])');
const toast = document.getElementById('toast');

function showToast(message, type='success') {
    toast.textContent = message;
    toast.classList.remove('bg-green-500','bg-red-500');
    toast.classList.add(type === 'success' ? 'bg-green-500' : 'bg-red-500');
    toast.style.opacity = 1;
    toast.style.pointerEvents = 'auto';
    setTimeout(() => {
        toast.style.opacity = 0;
        toast.style.pointerEvents = 'none';
    }, 3000);
}

// Toggle edit/save
editBtn.addEventListener('click', () => {
    if(editBtn.textContent === 'Edit') {
        inputs.forEach(input => input.removeAttribute('readonly'));
        editBtn.textContent = 'Save';
    } else {
        // Save via AJAX
        const formData = new FormData(form);
        fetch('', { method:'POST', body: formData })
            .then(r => r.text())
            .then(text => {
                try {
                    const d = JSON.parse(text);
                    if(d.error) showToast(d.error, 'error');
                    if(d.success) {
                        showToast(d.success, 'success');
                        inputs.forEach(input => input.setAttribute('readonly', true));
                        editBtn.textContent = 'Edit';
                    }
                } catch(err) {
                    console.error('Invalid JSON:', text);
                    showToast('Unexpected server response', 'error');
                }
            })
            .catch(() => showToast('Network error!', 'error'));
    }
});
</script>

</body>
</html>

<?php
include("footer.php");
?>
