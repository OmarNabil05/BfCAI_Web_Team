<!-- Footer -->
<footer class="footer">
  <div class="footer-container">
    <div class="footer-grid">
      <!-- About Us Section -->
      <div class="footer-section">
        <h3 class="footer-brand">Pizza Fiesta</h3>
        <div class="footer-social">
          <a href="#" class="social-icon" aria-label="Twitter">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
              <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
            </svg>
          </a>
          <a href="#" class="social-icon" aria-label="Facebook">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
              <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
            </svg>
          </a>
          <a href="#" class="social-icon" aria-label="Instagram">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
              <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
              <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z" fill="#1a1a1a"/>
              <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke="#1a1a1a" stroke-width="2"/>
            </svg>
          </a>
        </div>
      </div>

      <!-- About Us Description -->
      <div class="footer-section">
        <h4 class="footer-heading">ABOUT US</h4>
        <p class="footer-text">
          Passionate about delivering unforgettable pizza experiences with every savory slice we create.
        </p>
      </div>

      <!-- Opening Hours -->
      <div class="footer-section">
        <h4 class="footer-heading">OPENING HOURS</h4>
        <p class="footer-text">Monday - Friday</p>
        <p class="footer-text">8:00am - 9:00pm</p>
      </div>

      <!-- Services -->
      <div class="footer-section">
        <h4 class="footer-heading">SERVICES</h4>
        <ul class="footer-list">
          <li><a href="#" class="footer-link">Dine-In</a></li>
          <li><a href="#" class="footer-link">Online Ordering</a></li>
          <li><a href="#" class="footer-link">Catering</a></li>
          <li><a href="#" class="footer-link">Specialty Pizzas</a></li>
        </ul>
      </div>

      <!-- Contact Info -->
      <div class="footer-section">
        <h4 class="footer-heading">HAVE A QUESTION?</h4>
        <div class="footer-contact">
          <div class="contact-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
              <circle cx="12" cy="10" r="3"/>
            </svg>
            <span>20 Graham Rd., Malvern WR14 2HL, United Kingdom</span>
          </div>
          <div class="contact-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/>
            </svg>
            <span>+44 168 4892 229</span>
          </div>
          <div class="contact-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
              <polyline points="22,6 12,13 2,6"/>
            </svg>
            <span>info@pizzafiesta.com</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Search Icon (Right side) -->
    <div class="footer-search">
      <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"/>
        <path d="m21 21-4.35-4.35"/>
      </svg>
    </div>
  </div>

  <!-- Copyright -->
  <div class="footer-copyright">
    <p>Copyright © <?php echo date('Y'); ?> All rights reserved</p>
  </div>
</footer>

<style>
  .footer {
    background-color: #0a0a0a;
    color: #b0b0b0;
    padding: 3rem 0 1rem 0;
    margin-top: 4rem;
    border-top: 1px solid #222;
  }

  .footer-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2rem;
    position: relative;
  }

  .footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
  }

  .footer-section {
    padding: 0 1rem;
  }

  .footer-brand {
    color: #f0c040;
    font-size: 1.5rem;
    font-weight: bold;
    font-family: 'Georgia', serif;
    margin-bottom: 1rem;
  }

  .footer-social {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
  }

  .social-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #f0f0f0;
    color: #1a1a1a;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
  }

  .social-icon:hover {
    background-color: #f0c040;
    transform: scale(1.1);
  }

  .footer-heading {
    color: #f0f0f0;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 1rem;
    letter-spacing: 0.5px;
  }

  .footer-text {
    color: #b0b0b0;
    font-size: 0.9rem;
    line-height: 1.6;
    margin-bottom: 0.5rem;
  }

  .footer-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .footer-list li {
    margin-bottom: 0.5rem;
  }

  .footer-link {
    color: #b0b0b0;
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.3s ease;
  }

  .footer-link:hover {
    color: #f0c040;
  }

  .footer-contact {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .contact-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    color: #b0b0b0;
    font-size: 0.9rem;
  }

  .contact-item svg {
    flex-shrink: 0;
    margin-top: 0.2rem;
    color: #f0c040;
  }

  .footer-search {
    position: absolute;
    bottom: 0;
    right: 3rem;
    width: 60px;
    height: 60px;
    background-color: #f0c040;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .footer-search:hover {
    background-color: #ffdb70;
    transform: scale(1.1);
  }

  .footer-search svg {
    color: #1a1a1a;
  }

  .footer-copyright {
    text-align: center;
    padding: 1.5rem 0;
    border-top: 1px solid #222;
    margin-top: 2rem;
  }

  .footer-copyright p {
    color: #808080;
    font-size: 0.85rem;
    margin: 0;
  }

  @media (max-width: 768px) {
    .footer-grid {
      grid-template-columns: 1fr;
    }

    .footer-search {
      position: static;
      margin: 2rem auto 0;
    }

    .footer-container {
      padding: 0 1rem;
    }
  }
</style>
