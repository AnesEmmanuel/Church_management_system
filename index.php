<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Welcome | Church Management System</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
body {
  font-family: 'Segoe UI', sans-serif;
  background: #f5f7fa;
  margin: 0;
  color: #333;
}
.navbar { background: #0b6efd; }
.navbar .navbar-brand, .navbar .nav-link { color: white !important; }
.hero {
  position: relative;
  color: white;
  text-align: center;
  padding: 100px 20px;
  background: linear-gradient(135deg, rgba(11,110,253,0.8), rgba(10,88,202,0.8)), url('hero-bg.jpg') center/cover no-repeat;
}
.hero h1 { font-size: 3rem; font-weight: bold; animation: fadeInDown 1s; }
.hero p { font-size: 1.3rem; margin-top: 15px; animation: fadeInUp 1.2s; }
.hero .btn { margin-top: 25px; padding: 14px 35px; font-size: 1.2rem; border-radius: 30px; animation: fadeIn 1.5s; }
@keyframes fadeInDown { from {opacity:0;transform:translateY(-20px);} to {opacity:1;transform:translateY(0);} }
@keyframes fadeInUp { from {opacity:0;transform:translateY(20px);} to {opacity:1;transform:translateY(0);} }
@keyframes fadeIn { from {opacity:0;} to {opacity:1;} }
.section { padding: 70px 20px; }
.section h2 { text-align: center; margin-bottom: 50px; font-weight: bold; color: #0b6efd; }
.card-custom { border: none; border-radius: 12px; box-shadow: 0 3px 12px rgba(0,0,0,0.08); transition: 0.3s; }
.card-custom:hover { transform: translateY(-5px); box-shadow: 0 6px 20px rgba(0,0,0,0.15); }
.card-icon { font-size: 2.5rem; margin-bottom: 15px; color: #0b6efd; }
footer { background: #0b6efd; color: white; text-align: center; padding: 30px 20px; margin-top: 40px; }
footer a { color: white; text-decoration: underline; }
@media (max-width: 768px) {
  .hero h1 { font-size: 2rem; }
  .hero p { font-size: 1rem; }
  .hero .btn { font-size: 1rem; padding: 12px 25px; }
}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="#">KYEBITEMBE PARISH</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon" style="color:white;"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
        <li class="nav-item"><a class="nav-link" href="#ministries">Ministries</a></li>
        <li class="nav-item"><a class="nav-link" href="#leaders">Leaders</a></li>
        <li class="nav-item"><a class="nav-link" href="#events">Events</a></li>
        <li class="nav-item"><a class="nav-link" href="#donate">Donate</a></li>
        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero Section -->
<div class="hero">
  <h1>Welcome to Our Church</h1>
  <p>“For where two or three gather in my name, there am I with them.” – Matthew 18:20</p>
  <a href="login.php" class="btn btn-light">Login to CMS</a>
</div>

<!-- About Section -->
<div id="about" class="section bg-light">
  <div class="container">
    <h2>About Us</h2>
    <p class="text-center">We are a faith-driven community dedicated to serving God and His people. Our mission is to spread love, hope, and faith through worship, fellowship, and service.</p>
  </div>
</div>

<!-- Ministries Section -->
<div id="ministries" class="section">
  <div class="container">
    <h2>Our Ministries</h2>
    <div class="row g-4 text-center">
      <div class="col-md-4">
        <div class="card card-custom p-4">
          <div class="card-icon"><i class="fas fa-music"></i></div>
          <h4>Choir</h4>
          <p>Praising God through music and worship.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-custom p-4">
          <div class="card-icon"><i class="fas fa-praying-hands"></i></div>
          <h4>Prayer Group</h4>
          <p>Interceding and supporting each other in prayer.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-custom p-4">
          <div class="card-icon"><i class="fas fa-users"></i></div>
          <h4>Youth Ministry</h4>
          <p>Empowering young people in faith and service.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Parish Leaders Section -->
<div id="leaders" class="section bg-light">
  <div class="container">
    <h2>Our Parish Leaders</h2>
    <div class="row g-4 text-center">

      <div class="col-md-4">
        <div class="card card-custom p-4">
          <img src="images/profile.png" alt="Chairperson" class="rounded-circle mb-3" width="120" height="120">
          <h4>Mr. John Doe</h4>
          <p><strong>Chairperson</strong></p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card card-custom p-4">
          <img src="images/profile.png" alt="Assistant Chairperson" class="rounded-circle mb-3" width="120" height="120">
          <h4>Mrs. Jane Smith</h4>
          <p><strong>Assistant Chairperson</strong></p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card card-custom p-4">
          <img src="images/profile.png" alt="Secretary" class="rounded-circle mb-3" width="120" height="120">
          <h4>Mr. Michael Brown</h4>
          <p><strong>Secretary</strong></p>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card card-custom p-4">
          <img src="images/profile.png" alt="Assistant Secretary" class="rounded-circle mb-3" width="120" height="120">
          <h4>Ms. Emily Davis</h4>
          <p><strong>Assistant Secretary</strong></p>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card card-custom p-4">
          <img src="images/profile.png" alt="Accountant" class="rounded-circle mb-3" width="120" height="120">
          <h4>Mr. Peter Wilson</h4>
          <p><strong>Accountant</strong></p>
          <p><strong>Phone:0787829229</strong></p>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Events Section -->
<div id="events" class="section bg-light">
  <div class="container">
    <h2>Upcoming Events</h2>
    <div class="row g-4">
      <div class="col-md-6">
        <div class="card card-custom p-4">
          <h5>📅 Sunday Worship</h5>
          <p>Join us every Sunday at 10:00 AM for fellowship and worship.</p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card card-custom p-4">
          <h5>🤝 Community Outreach</h5>
          <p>Serving the community with love and care – Next outreach on Sept 15.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Testimonials -->
<div class="section">
  <div class="container">
    <h2>What Our Members Say</h2>
    <div class="row g-4">
      <div class="col-md-6">
        <div class="card card-custom p-4">
          <p>“Being part of this church has strengthened my faith and given me a supportive community.”</p>
          <strong>- John M.</strong>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card card-custom p-4">
          <p>“The ministries here provide meaningful ways to serve and grow spiritually.”</p>
          <strong>- Sarah L.</strong>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Donation Call to Action -->
<div id="donate" class="section text-center">
  <div class="container">
    <h2>Support Our Mission</h2>
    <p>Your contributions help us continue spreading the word of God and supporting those in need.</p>
    <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#donateModal">💰 Donate Now</button>
  </div>
</div>

<!-- Donate Modal -->
<div class="modal fade" id="donateModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Choose Payment Network</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="donateForm">
          <label for="network" class="form-label">Select Mobile Network:</label>
          <select id="network" class="form-select" required>
            <option value="">-- Select --</option>
            <option value="vodacom">Vodacom M-Pesa</option>
            <option value="airtel">Airtel Money</option>
            <option value="halotel">Halotel Money</option>
            <option value="tigo">Tigo Pesa</option>
            <option value="ttcl">TTCL Pesa</option>
          </select>
        </form>
        <div id="paymentInstructions" class="mt-3"></div>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer>
  <p>&copy; <?php echo date("Y"); ?> Church Management System | All Rights Reserved</p>
  <p><a href="#about">About</a> | <a href="#ministries">Ministries</a> | <a href="#leaders">Leaders</a> | <a href="#events">Events</a> | <a href="#donate">Donate</a></p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById("network").addEventListener("change", function(){
  const instructions = document.getElementById("paymentInstructions");
  let msg = "";
  switch(this.value){
    case "vodacom":
      msg = `<h6>Vodacom M-Pesa</h6>
             <p>Dial <b>*150*00#</b> → Lipa na M-Pesa.</p>
             <p>Church Account: <b>0777 123 456</b></p>
             <p>Reference: <b>Church Donation</b></p>`;
      break;
    case "airtel":
      msg = `<h6>Airtel Money</h6>
             <p>Dial <b>*150*60#</b> → Pay Bill.</p>
             <p>Account: <b>0788 987 654</b></p>`;
      break;
    case "halotel":
      msg = `<h6>Halotel Money</h6>
             <p>Dial <b>*150*88#</b> and follow prompts.</p>`;
      break;
    case "tigo":
      msg = `<h6>Tigo Pesa</h6>
             <p>Dial <b>*150*01#</b> → Pay Bill.</p>`;
      break;
    case "ttcl":
      msg = `<h6>TTCL Pesa</h6>
             <p>Dial <b>*150*71#</b> → Pay Bill.</p>`;
      break;
    default:
      msg = "";
  }
  instructions.innerHTML = msg;
});
</script>
</body>
</html>
