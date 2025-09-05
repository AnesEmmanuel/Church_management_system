<?php
// MUST be the very first PHP output (no whitespace/BOM before this)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';
<<<<<<< HEAD
require_once 'lang.php'; // include translation system
=======
>>>>>>> 391d3a86310207ad560b6208a34c7cb4a99ddf3c

// If user not logged in, redirect
if (empty($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// --- Fetch user profile ---
$username = $_SESSION['user'];
$stmt = $conn->prepare("SELECT id, name, profile_pic FROM leaders WHERE name=? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$user_res = $stmt->get_result()->fetch_assoc();
$stmt->close();

$profile_pic = !empty($user_res['profile_pic']) ? $user_res['profile_pic'] : 'images/profile.png';
$display_name = !empty($user_res['name']) ? $user_res['name'] : $username;

// Helper to count rows in a table
function count_table($conn, $table) {
    $table = preg_replace('/[^a-z0-9_]/i', '', $table);
    $sql = "SELECT COUNT(*) AS c FROM `$table`";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();
        return (int)($row['c'] ?? 0);
    }
    return 0;
}

// --- Counts for the pie chart ---
$counts = [
  'leaders'   => count_table($conn,'leaders'),
  'donations' => count_table($conn,'donations'),
  'events'    => count_table($conn,'events'),
  'kipaimara' => count_table($conn,'kipaimara'),
  'communities' => count_table($conn,'communities'),
  'units' => count_table($conn,'units'),
  'ubatizo'   => count_table($conn,'ubatizo'),
<<<<<<< HEAD
  'messages'  => count_table($conn,'messages')
=======
  'ubatizo'   => count_table($conn,'ubatizo'),
  'messages'    => count_table($conn,'messages') 

>>>>>>> 391d3a86310207ad560b6208a34c7cb4a99ddf3c
];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Church Management System</title>
<link rel="stylesheet" href="cs/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  body { margin:0; font-family: Arial, sans-serif; display:flex; min-height:100vh; overflow:hidden; }
  body::before { content:""; position:fixed; top:0; left:0; right:0; bottom:0; background-size:cover; background-position:center; z-index:-2; animation: slideShow 24s infinite; }
  @keyframes slideShow { 0%{background-image:url('images/download.jfif');} 33%{background-image:url('images/parokia.jfif');} 66%{background-image:url('images/pr.jfif');} 100%{background-image:url('images/parokia.jfif');} }
  body::after { content:""; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.45); z-index:-1; }

  .sidebar { width:240px; background:rgba(44,62,80,0.9); color:#fff; padding:20px 10px; position:fixed; height:100%; display:flex; flex-direction:column; }
  .sidebar h2 { text-align:center; margin:16px 0; font-size:18px; }
  .sidebar a { color:#ecf0f1; text-decoration:none; padding:10px 16px; display:block; border-radius:6px; margin:6px 8px; transition:background .15s; }
  .sidebar a:hover { background:#34495e; color:#fff; }
  .sidebar .logout { margin-top:auto; background:#c0392b; text-align:center; padding:10px; display:block; border-radius:6px; margin:16px 8px; }

  .profile { text-align:center; margin-bottom:20px; }
  .profile img { width:80px; height:80px; border-radius:50%; object-fit:cover; border:2px solid #fff; }
  .profile p { margin-top:10px; font-weight:bold; }

  .main { margin-left:240px; padding:20px; flex:1; color:#fff; }
  .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; }
  .welcome { margin-bottom:18px; background:rgba(255,255,255,0.1); padding:14px 18px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.2); }

  .chart-container { max-width:600px; margin:30px auto; background: rgba(255,255,255,0.1); padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.2); height:400px; }
<<<<<<< HEAD
  .lang-switch a { color:#fff; margin-left:10px; text-decoration:none; }
=======
>>>>>>> 391d3a86310207ad560b6208a34c7cb4a99ddf3c
</style>
</head>
<body>

<div class="sidebar">
  <div class="profile">
    <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture">
    <p><?php echo htmlspecialchars($display_name); ?></p>
  </div>

<<<<<<< HEAD
  <h2><?php echo __t('quick_actions'); ?></h2>
  <a href="leaders.php">👥 <?php echo __t('leaders'); ?> (<?php echo $counts['leaders']; ?>)</a>
  <a href="donations.php">💰 <?php echo __t('donations'); ?> (<?php echo $counts['donations']; ?>)</a>
  <a href="events.php">📅 <?php echo __t('events'); ?> (<?php echo $counts['events']; ?>)</a>
  <a href="members.php">🧑‍🤝‍🧑 <?php echo __t('members'); ?></a> 
  <a href="kipaimara.php">👶 <?php echo __t('kipaimara'); ?> (<?php echo $counts['kipaimara']; ?>)</a>
  <a href="messages.php">✉️ <?php echo __t('messages'); ?> (<?php echo $counts['messages']; ?>)</a>
  <a href="ubatizo.php">💧 <?php echo __t('ubatizo'); ?> (<?php echo $counts['ubatizo']; ?>)</a>
  <a href="logout.php" class="logout">🚪 <?php echo __t('logout'); ?></a>
=======
  <h2>Quick Actions</h2>
  <a href="leaders.php">👥 Leaders (<?php echo $counts['leaders']; ?>)</a>
  <a href="donations.php">💰 Donations (<?php echo $counts['donations']; ?>)</a>
  <a href="events.php">📅 Events (<?php echo $counts['events']; ?>)</a>
  <a href="members.php">🧑‍🤝‍🧑 Members</a> 
  <a href="kipaimara.php">👶 Kipaimara (<?php echo $counts['kipaimara']; ?>)</a>
  <!-- <a href="communities.php">📅 Community (<?php echo $counts['communities']; ?>)</a> -->
  <!-- <a href="units.php">👶 Unity (<?php echo $counts['units']; ?>)</a> -->
  <a href="messages.php">👶 Message (<?php echo $counts['messages']; ?>)</a>
  <a href="ubatizo.php">💧 Ubatizo (<?php echo $counts['ubatizo']; ?>)</a>
  <a href="logout.php" class="logout">🚪 Logout</a>
>>>>>>> 391d3a86310207ad560b6208a34c7cb4a99ddf3c
</div>

<div class="main">
  <div class="header">
    <h1>Kyebitembe Parish Management System</h1>
<<<<<<< HEAD
    <div class="right">
      <?php echo __t('welcome'); ?>, <?php echo htmlspecialchars($display_name); ?>
      <span class="lang-switch">
        | <a href="?lang=en">🇬🇧 EN</a>
        | <a href="?lang=sw">🇹🇿 SW</a>
      </span>
    </div>
  </div>

  <div class="welcome">
    <h2><?php echo __t('welcome'); ?>, <?php echo htmlspecialchars($display_name); ?> 👋</h2>
=======
    <div class="right">Logged in: <?php echo htmlspecialchars($display_name); ?></div>
  </div>

  <div class="welcome">
    <h2>Welcome, <?php echo htmlspecialchars($display_name); ?> 👋</h2>
>>>>>>> 391d3a86310207ad560b6208a34c7cb4a99ddf3c
    <p>Manage leaders, communities, donations, and church events all in one place.</p>
  </div>

  <section>
<<<<<<< HEAD
    <h2><?php echo __t('dashboard'); ?></h2>
=======
    <h2>Dashboard Overview</h2>
>>>>>>> 391d3a86310207ad560b6208a34c7cb4a99ddf3c

    <!-- Pie Chart -->
    <div class="chart-container">
      <canvas id="dashboardChart"></canvas>
    </div>
  </section>
</div>

<?php include 'footer.php'; ?>

<script>
const ctx = document.getElementById('dashboardChart').getContext('2d');
new Chart(ctx, {
    type: 'pie',
    data: {
<<<<<<< HEAD
        labels: [
            "<?php echo __t('leaders'); ?>",
            "<?php echo __t('donations'); ?>",
            "<?php echo __t('events'); ?>",
            "<?php echo __t('kipaimara'); ?>",
            "<?php echo __t('messages'); ?>",
            "<?php echo __t('ubatizo'); ?>"
        ],
=======
        labels: ['Leaders', 'Donations', 'Events', 'Kipaimara', 'Ubatizo','messages'],
>>>>>>> 391d3a86310207ad560b6208a34c7cb4a99ddf3c
        datasets: [{
            label: 'Total Count',
            data: [
                <?php echo $counts['leaders']; ?>,
                <?php echo $counts['donations']; ?>,
                <?php echo $counts['events']; ?>,
                <?php echo $counts['kipaimara']; ?>,
<<<<<<< HEAD
                <?php echo $counts['messages']; ?>,
=======
                //  <?php echo $counts['communities']; ?>,
                //   <?php echo $counts['units']; ?>,
                   <?php echo $counts['messages']; ?>,
>>>>>>> 391d3a86310207ad560b6208a34c7cb4a99ddf3c
                <?php echo $counts['ubatizo']; ?>
            ],
            backgroundColor: [
                'rgba(52, 152, 219, 0.7)',
                'rgba(46, 204, 113, 0.7)',
                'rgba(241, 196, 15, 0.7)',
                'rgba(231, 76, 60, 0.7)',
<<<<<<< HEAD
                'rgba(155, 89, 182, 0.7)',
                'rgba(52, 73, 94, 0.7)'
=======
                'rgba(155, 89, 182, 0.7)'
>>>>>>> 391d3a86310207ad560b6208a34c7cb4a99ddf3c
            ],
            borderColor: [
                'rgba(52, 152, 219,1)',
                'rgba(46, 204, 113,1)',
                'rgba(241, 196, 15,1)',
                'rgba(231, 76, 60,1)',
<<<<<<< HEAD
                'rgba(155, 89, 182,1)',
                'rgba(52, 73, 94,1)'
=======
                'rgba(155, 89, 182,1)'
>>>>>>> 391d3a86310207ad560b6208a34c7cb4a99ddf3c
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { color: '#fff', font: { size: 14, weight: 'bold' } } },
            tooltip: { callbacks: { label: function(context){ return context.label + ': ' + context.raw; } } }
        }
    }
});
</script>

<<<<<<< HEAD

<H3>NAije </H3>

=======
>>>>>>> 391d3a86310207ad560b6208a34c7cb4a99ddf3c
</body>
</html>
