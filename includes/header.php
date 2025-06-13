<link rel="stylesheet" href="../css/style.css">




<nav class="navbar">
<div class="upload-section">
    <form action="upload.php" method="post" enctype="multipart/form-data" style="display: flex; flex-direction: column;">
        <input type="file" name="profileImage" accept="image/*" required />
        <button type="submit" name="upload">Upload Photo</button>
    </form>
    <form action="upload.php" method="post">
        <button type="submit" name="remove">Remove Photo</button>
    </form>
</div>

    <div class="logo">
        <img src="../images/logo.png" alt="Logo" class="icon">
    </div>
    <div class="menu-toggle" id="menu-toggle">&#9776;</div>
    <ul class="menu" id="menu">
        <li><a href="#">Home</a></li>
        <li><a href="#">About</a></li>
        <li class="dropdown">
            <a href="#">Services ▾</a>
            <ul class="dropdown-menu">
                <li><a href="#">Web Design</a></li>
                <li><a href="#">App Development</a></li>
                <li><a href="#">SEO</a></li>
            </ul>
        </li>
        <li><a href="#">Contact</a></li>
    </ul>
    <div class="profile">
    <?php
$profileImg = glob("../uploads/profile.*");
$profileSrc = count($profileImg) ? $profileImg[0] . "?v=" . time() : "../images/default.png";
?>
<img src="<?php echo $profileSrc; ?>" alt="Profile">


    </div>




</nav>
