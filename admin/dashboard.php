<?php
session_start();
include('../includes/header.php');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

include('../config/database.php');
include('../includes/sidebar.php');


/* ===============================
   🔴 HANDLE IMAGE UPLOAD
================================ */
if(isset($_POST['upload'])){

    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $about = $_POST['about'];

    $image_name = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    $path = "../uploads/" . $dev;

    move_uploaded_file($tmp, $path);

    // Insert or update (single profile)
    mysqli_query($conn,
    "INSERT INTO developer_profile (name,email,address,about,image)
     VALUES ('$name','$email','$address','$about','$image_name')
     ON DUPLICATE KEY UPDATE
     name='$name',
     email='$email',
     address='$address',
     about='$about',
     image='$image_name'");
}


/* ===============================
   🔴 HANDLE DELETE IMAGE
================================ */
if(isset($_POST['delete'])){

    $get = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM developer_profile LIMIT 1"));

    if($get && $get['image']){
        unlink("../uploads/".$get['image']);
    }

    mysqli_query($conn, "UPDATE developer_profile SET image=NULL");
}


/* ===============================
   🔴 FETCH DEVELOPER DATA
================================ */
$dev = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM developer_profile LIMIT 1"));



/* ===============================
   🔴 DASHBOARD COUNTS
================================ */
$total_users = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as total FROM users WHERE role != 'admin'"))['total'];

$pending_users = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as total FROM users WHERE account_status='pending'"))['total'];

$total_complaints = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as total FROM complaints"))['total'];

$pending_complaints = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as total FROM complaints WHERE status='pending'"))['total'];

$resolved_complaints = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as total FROM complaints WHERE status='resolved'"))['total'];
?>

<h1>Admin Dashboard</h1>


<!-- ===============================
     🔴 DEVELOPER PROFILE SECTION (TOP PART)
================================ -->
<div style="border:1px solid #ccc; padding:15px; margin-bottom:20px;">

<h2>Developer Profile</h2>

<?php if($dev && $dev['image']): ?>
    <img src="../uploads/<?php echo $dev['image']; ?>" width="150"><br><br>
<?php endif; ?>

<p><strong>Name:</strong> <?php echo $dev['name'] ?? 'N/A'; ?></p>
<p><strong>Email:</strong> <?php echo $dev['email'] ?? 'N/A'; ?></p>
<p><strong>Address:</strong> <?php echo $dev['address'] ?? 'N/A'; ?></p>
<p><strong>About:</strong> <?php echo $dev['about'] ?? 'N/A'; ?></p>

<br>

<!-- 🔴 UPLOAD / UPDATE FORM -->
<form method="POST" enctype="multipart/form-data">

    <!-- 🔴 YOU CAN EDIT THESE -->
    <input type="text" name="name" placeholder="Your Name" required><br><br>
    <input type="email" name="email" placeholder="Your Email" required><br><br>
    <input type="text" name="address" placeholder="Your Address" required><br><br>
    <textarea name="about" placeholder="About You" required></textarea><br><br>

    <!-- 🔴 IMAGE INPUT -->
    <input type="file" name="image" required><br><br>

    <button name="upload">Upload / Save</button>

</form>

<br>

<!-- 🔴 DELETE IMAGE BUTTON -->
<form method="POST">
    <button name="delete">Delete Image</button>
</form>

</div>


<!-- ===============================
     🔴 ORIGINAL DASHBOARD CARDS
================================ -->

<div class="cards">

    <div class="card">
        <h3><?php echo $total_users; ?></h3>
        <p>Total Users</p>
    </div>

    <div class="card">
        <h3><?php echo $pending_users; ?></h3>
        <p>Pending Users</p>
    </div>

    <div class="card">
        <h3><?php echo $total_complaints; ?></h3>
        <p>Total Complaints</p>
    </div>

    <div class="card">
        <h3><?php echo $pending_complaints; ?></h3>
        <p>Pending Complaints</p>
    </div>

    <div class="card">
        <h3><?php echo $resolved_complaints; ?></h3>
        <p>Resolved Complaints</p>
    </div>

</div>

<a href="../auth/logout.php">Logout</a>

<?php include('../includes/footer.php'); ?>