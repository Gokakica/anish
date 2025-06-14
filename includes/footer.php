<footer class="footer">
  <div class="footer-container">
    <div class="footer-section">
      <h4>About Us</h4>
      <p>We provide high-quality Web, Mobile, and SEO solutions to grow your digital presence.</p>
    </div>

    <div class="footer-section">
      <h4>Quick Links</h4>
      <ul class="footer-links">
        <li><a href="#">Home</a></li>
        <li><a href="#">Services</a></li>
        <li><a href="#">Contact</a></li>
      </ul>
    </div>
  </div>

  <div class="footer-bottom">
    &copy; 2025 <strong>My Simple Website</strong>. All rights reserved.
  </div>
</footer>

<style>
  .footer {
    background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
    color: #ffffff;
    padding: 40px 20px 20px;
    font-family: 'Segoe UI', sans-serif;
  }

  .footer-container {
    max-width: 1200px;
    margin: auto;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 40px;
  }

  .footer-section {
    flex: 1;
    min-width: 240px;
  }

  .footer-section h4 {
    font-size: 18px;
    margin-bottom: 15px;
    color: #ffdd57;
    position: relative;
  }

  .footer-section p {
    font-size: 14px;
    line-height: 1.6;
    color: #ddd;
  }

  .footer-links {
    list-style: none;
    padding: 0;
  }

  .footer-links li {
    margin: 10px 0;
  }

  .footer-links a {
    text-decoration: none;
    color: #ccc;
    transition: color 0.3s ease;
  }

  .footer-links a:hover {
    color: #ffffff;
  }

  .footer-bottom {
    text-align: center;
    padding-top: 25px;
    font-size: 14px;
    color: #aaa;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    margin-top: 30px;
  }

  @media (max-width: 768px) {
    .footer-container {
      flex-direction: column;
      align-items: center;
      text-align: center;
    }
  }
</style>
