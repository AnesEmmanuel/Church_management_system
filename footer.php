<?php
// footer.php
?>
<footer class="footer">
  <p>&copy; <?php echo date("Y"); ?> Kyebitembe Parish Management System. All rights reserved.</p>
  <p>Developed by <strong>Your Team</strong></p>
</footer>

<style>
  .footer {
    background: rgba(44, 62, 80, 0.9);
    color: #ecf0f1;
    text-align: center;
    padding: 12px;
    position: fixed;
    bottom: 0;
    left: 240px; /* leave space for sidebar on desktop */
    right: 0;
    font-size: 14px;
    box-shadow: 0 -2px 6px rgba(0,0,0,0.3);
    z-index: 100;
  }

  /* For pages without sidebar (like login.php) */
  body.no-sidebar .footer {
    left: 0;
  }

  /* Responsive for smaller screens */
  @media (max-width: 768px) {
    .footer {
      left: 0; /* sidebar usually collapses on mobile */
      font-size: 12px;
      padding: 10px;
    }
  }

  /* Push page content above footer */
  .main {
    padding-bottom: 60px; /* reserve space so footer won’t overlap */
  }
</style>
