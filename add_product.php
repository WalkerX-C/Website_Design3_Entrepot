<?php
session_start();

if (!isset($_SESSION["sellerID"])) {
    $_SESSION["error_msg"] = "Please login as a seller before registering a product.";
    header("Location: seller_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Product Launch Center Pro</title>
<style>
    :root {
    --primary: #0071e3;
    --text-main: #1d1d1f;
    --text-sec: #86868b;
    --bg-main: #f5f5f7;
    --bg-card: #ffffff;
    --border: #d2d2d7;
    --glass-bg: rgba(255, 255, 255, 0.5);
    --glass-border: rgba(255, 255, 255, 0.6);
    --blob-1: #ffb2e6;
    --blob-2: #a1c4fd;
    --blob-3: #c2e9fb;
    --gradient-1: #f0f4fd;
    --gradient-2: #f5f5f7;
    --gradient-3: #e0eafc;
    --gradient-4: #cfdef3;
  }

  [data-theme="dark"] {
    --primary: #0a84ff;
    --text-main: #f5f5f7;
    --text-sec: #a1a1a6;
    --bg-main: #000000;
    --bg-card: #1c1c1e;
    --border: #38383a;
    --glass-bg: rgba(28, 28, 30, 0.6);
    --glass-border: rgba(255, 255, 255, 0.1);
    --blob-1: #4a004a;
    --blob-2: #002244;
    --blob-3: #003333;
    --gradient-1: #0f0f13;
    --gradient-2: #000000;
    --gradient-3: #13131a;
    --gradient-4: #0a0a0f;
  }
  
  body {
    font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Text', 'Helvetica Neue', sans-serif;
    color: var(--text-main);
    margin: 0;
    padding: 0;
    -webkit-font-smoothing: antialiased;
    background: linear-gradient(-45deg, var(--gradient-1), var(--gradient-2), var(--gradient-3), var(--gradient-4));
    background-size: 400% 400%;
    animation: gradientFlow 10s ease infinite;
    transition: color 0.5s ease;
    overflow-x: hidden;
  }

  @keyframes gradientFlow {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }

  .bg-blobs {
    position: fixed;
    top: 0; left: 0; width: 100vw; height: 100vh;
    z-index: -1;
    overflow: hidden;
  }
  
  .blob {
    position: absolute;
    filter: blur(80px);
    -webkit-filter: blur(80px);
    border-radius: 50%;
    opacity: 0.7;
    will-change: transform;
    transition: background 0.5s ease;
  }

  .blob-1 {
    width: 600px; height: 600px;
    background: var(--blob-1);
    top: -100px; left: -100px;
    animation: float1 8s infinite ease-in-out alternate;
  }

  .blob-2 {
    width: 700px; height: 700px;
    background: var(--blob-2);
    bottom: -200px; right: -100px;
    animation: float2 10s infinite ease-in-out alternate-reverse;
  }

  .blob-3 {
    width: 500px; height: 500px;
    background: var(--blob-3);
    top: 30%; left: 40%;
    animation: float3 9s infinite ease-in-out alternate;
  }

  @keyframes float1 {
    0% { transform: translate3d(0, 0, 0) scale(1); }
    100% { transform: translate3d(20vw, 15vh, 0) scale(1.2); }
  }

  @keyframes float2 {
    0% { transform: translate3d(0, 0, 0) scale(1); }
    100% { transform: translate3d(-25vw, -20vh, 0) scale(1.1); }
  }

  @keyframes float3 {
    0% { transform: translate3d(0, 0, 0) scale(1); }
    100% { transform: translate3d(15vw, -25vh, 0) scale(1.3); }
  }

  .top-header,
  .nav-bar {
    width: 92%;
    max-width: 1440px;
    margin-left: auto;
    margin-right: auto;
    position: relative;
    z-index: 10;
  }

  .top-header {
    padding: 22px 0 12px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
  }

  .title-area h1 {
    margin: 0;
    font-size: 48px;
    color: var(--text-main);
    transition: color 0.5s ease;
  }

  .title-sub {
    margin-top: 8px;
    font-size: 16px;
    color: var(--text-sec);
    transition: color 0.5s ease;
  }

  .header-controls {
    display: flex;
    align-items: center;
    gap: 20px;
  }

  .theme-toggle {
    background: var(--bg-card);
    border: 1px solid var(--border);
    color: var(--text-main);
    padding: 10px 20px;
    border-radius: 30px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  }

  .theme-toggle:hover {
    transform: translateY(-2px);
  }

  .header-icon a {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    width: 54px;
    height: 54px;
    border-radius: 16px;
    background: var(--bg-card);
    border: 1px solid var(--border);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
  }

  .header-icon img {
    width: 34px;
    height: 34px;
    object-fit: contain;
  }

  .nav-bar {
    margin-top: 4px;
    margin-bottom: 22px;
    background-color: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 24px;
    padding: 18px 28px;
    box-shadow: 0 8px 22px rgba(0, 0, 0, 0.05);
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 28px;
    flex-wrap: wrap;
    transition: background-color 0.5s ease, border 0.5s ease;
  }

  .nav-bar a {
    text-decoration: none;
    color: var(--text-main);
    font-size: 16px;
    font-weight: 500;
    padding: 8px 14px;
    transition: color 0.3s ease, transform 0.3s ease, background-color 0.3s ease;
  }

  .nav-bar a:hover {
    transform: scale(1.08);
    background-color: var(--border);
    border-radius: 9px;
  }

  @media only screen and (max-width: 700px) {
    .top-header {
      flex-direction: column;
      align-items: center;
      text-align: center;
      gap: 16px;
      padding-top: 24px;
    }
    .title-area h1 { font-size: 34px; }
    .title-sub { font-size: 15px; }
    .nav-bar { gap: 14px; }
    .header-controls { flex-direction: row; }
  }

  .main-wrapper {
    display: flex;
    min-height: calc(100vh - 200px);
    width: 100%;
    box-sizing: border-box;
    padding: 20px 40px 40px 40px;
    gap: 40px;
    position: relative;
    z-index: 10;
  }

  .preview-section {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    position: sticky;
    top: 40px;
    height: calc(100vh - 60px);
  }

  .device-preview {
    width: 100%;
    max-width: 440px;
    background: var(--glass-bg);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border: 1px solid var(--glass-border);
    border-radius: 32px;
    padding: 30px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.1);
    transition: all 0.5s ease;
  }

  .preview-image-container {
    width: 100%;
    height: 320px;
    background: var(--bg-card);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    margin-bottom: 24px;
    transition: background 0.5s ease;
  }

  .preview-image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: none;
  }

  .preview-placeholder {
    color: var(--text-sec);
    font-size: 15px;
    font-weight: 500;
  }

  .preview-info h3 {
    margin: 0 0 10px 0;
    font-size: 26px;
    font-weight: 700;
  }

  .preview-price {
    font-size: 22px;
    color: var(--primary);
    font-weight: 600;
    margin-bottom: 16px;
  }

  .preview-desc {
    font-size: 15px;
    color: var(--text-sec);
    line-height: 1.6;
  }

  .form-section {
    flex: 1.2;
    padding-bottom: 40px;
  }

  .form-card {
    background: var(--bg-card);
    padding: 50px;
    border-radius: 32px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.05);
    transition: all 0.5s ease;
  }

  .form-card h2 {
    font-size: 36px;
    margin-top: 0;
    margin-bottom: 40px;
    font-weight: 700;
  }

  .form-group {
    margin-bottom: 28px;
  }

  label {
    display: block;
    margin-bottom: 10px;
    font-size: 14px;
    color: var(--text-sec);
    font-weight: 600;
  }

  input[type="text"],
  input[type="number"],
  select,
  textarea {
    width: 100%;
    padding: 18px;
    border: 1.5px solid var(--border);
    border-radius: 14px;
    box-sizing: border-box;
    font-size: 16px;
    transition: all 0.3s;
    background-color: transparent;
    color: var(--text-main);
  }

  input:focus, 
  select:focus, 
  textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(0, 113, 227, 0.15);
  }

  .flex-row {
    display: flex;
    gap: 20px;
  }
  
  .flex-col {
    flex: 1;
  }

  .upload-area {
    border: 2px dashed var(--border);
    padding: 40px 20px;
    text-align: center;
    border-radius: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 16px;
  }
  
  .upload-area:hover {
    border-color: var(--primary);
    background: rgba(0, 113, 227, 0.05);
  }

  .thumbnail-list {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    min-height: 80px;
    padding: 10px;
    background: rgba(0,0,0,0.02);
    border-radius: 12px;
    border: 1px solid var(--border);
  }

  .thumb-item {
    width: 80px;
    height: 80px;
    border-radius: 10px;
    object-fit: cover;
    cursor: grab;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s;
  }

  .thumb-item:active {
    cursor: grabbing;
  }

  .thumb-item.dragging {
    opacity: 0.4;
    transform: scale(0.95);
  }

  .btn {
    background: var(--text-main);
    color: var(--bg-main);
    border: none;
    padding: 20px;
    width: 100%;
    border-radius: 16px;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 20px;
  }
  
  .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
  }

  @media only screen and (max-width: 1024px) {
    .main-wrapper { flex-direction: column; }
    .preview-section { position: relative; top: 0; height: auto; }
    .device-preview { max-width: 100%; }
  }
</style>
<link rel="stylesheet" href="theme.css">
</head>
<body>

<div class="bg-blobs">
  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>
  <div class="blob blob-3"></div>
</div>

<header class="top-header">
  <div class="title-area">
      <h1>MY Store</h1>
      <p class="title-sub">A simple and modern place to explore electronic products.</p>
  </div>
  <div class="header-controls">
      <button class="theme-toggle" id="themeToggle">Dark Mode</button>
      <div class="header-icon">
          <a href="homepage.php">
              <img src="home.png" alt="Home Icon">
          </a>
      </div>
  </div>
</header>

<nav class="nav-bar">
  <a href="purchaser_login.php">Buyer Login</a>
  <a href="search.php">Search</a>
  <a href="add_product.php">Product Registration</a>
  <a href="seller_login.php">Seller Login</a>
  <a href="bag.php">Bag</a>
</nav>

<!-- make it dispaly on the top of page -->
<?php if (isset($_GET['success']) && $_GET['success'] === '1'): ?>
  <div class="notice success">Product launched successfully.</div>
<?php endif; ?>

<div class="main-wrapper">
  
  <div class="preview-section">
    <div class="device-preview">
      <div class="preview-image-container">
        <span class="preview-placeholder" id="mainPlaceholder">No Image Selected</span>
        <img id="mainPreviewImg" alt="Main View">
      </div>
      <div class="preview-info">
        <h3 id="liveTitle">Product Title</h3>
        <div class="preview-price">$<span id="livePrice">0.00</span></div>
        <div class="preview-desc" id="liveDesc">Product description will appear here.</div>
      </div>
    </div>
  </div>

  <div class="form-section">
    <div class="form-card">
      <h2>List New Item</h2>
      <form id="productForm" action="process_add_product.php" method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
          <label for="productName">Title</label>
          <input type="text" id="productName" name="productName" required placeholder="Enter product name">
        </div>

        <div class="form-group flex-row">
          <div class="flex-col">
            <label for="price">Price USD</label>
            <input type="number" id="price" name="price" required min="0" step="0.01" placeholder="99.00">
          </div>
          <div class="flex-col">
            <label for="brand">Brand</label>
            <input type="text" id="brand" name="brand" required placeholder="Apple, Sony, etc.">
          </div>
        </div>

        <div class="form-group flex-row">
            <div class="flex-col">
                <label for="model">Model</label>
                <input type="text" id="model" name="model" required placeholder="Enter product model">
            </div>
            <div class="flex-col">
                <label for="colour">Colour</label>
                <input type="text" id="colour" name="colour" required placeholder="Enter product colour">
            </div>
        </div>

        <div class="form-group flex-row">
            <div class="flex-col">
                <label for="year">Year</label>
                <input type="number" id="year" name="year" required placeholder="2026">
            </div>
            <div class="flex-col">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" required placeholder="Beijing, Shanghai...">
            </div>
        </div>

        <div class="form-group">
          <label>Product Image</label>
          <div class="upload-area" onclick="document.getElementById('imageInput').click()">
            <span style="color: var(--primary); font-weight: 600;">Click to upload product image</span>
            <input type="file" id="imageInput" name="productImage" accept="image/*" style="display: none;" required>
          </div>
          <div class="thumbnail-list" id="thumbnailList"></div>
        </div>

        <div class="form-group">
          <label for="description">Story</label>
          <textarea id="description" name="sellerMessage" rows="5" required placeholder="Tell the story of your product"></textarea>
        </div>

        <button type="submit" class="btn">Launch Product</button>
      </form>
    </div>
  </div>

</div>

<script>
  const themeToggle = document.getElementById('themeToggle');
  themeToggle.addEventListener('click', () => {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    if (isDark) {
      document.documentElement.removeAttribute('data-theme');
      themeToggle.textContent = 'Dark Mode';
    } else {
      document.documentElement.setAttribute('data-theme', 'dark');
      themeToggle.textContent = 'Light Mode';
    }
  });

  document.getElementById('productName').addEventListener('input', e => {
    document.getElementById('liveTitle').textContent = e.target.value || 'Product Title';
  });
  document.getElementById('price').addEventListener('input', e => {
    document.getElementById('livePrice').textContent = e.target.value || '0.00';
  });
  document.getElementById('description').addEventListener('input', e => {
    document.getElementById('liveDesc').textContent = e.target.value || 'Product description will appear here.';
  });

  const imageInput = document.getElementById('imageInput');
  const thumbnailList = document.getElementById('thumbnailList');
  const mainPreviewImg = document.getElementById('mainPreviewImg');
  const mainPlaceholder = document.getElementById('mainPlaceholder');

  imageInput.addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            mainPreviewImg.src = e.target.result;
            mainPreviewImg.style.display = 'block';
            mainPlaceholder.style.display = 'none';
            
            thumbnailList.innerHTML = '';
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'thumb-item';
            thumbnailList.appendChild(img);
        };
        reader.readAsDataURL(file);
    }
  });

  window.onload = function() {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('success') === '1') {
          const btn = document.querySelector('.btn');
          btn.textContent = 'Launch Successful!';
          btn.style.background = '#32d74b';
          btn.style.color = '#fff';
          
          setTimeout(() => {
              btn.textContent = 'Launch Product';
              btn.style.background = 'var(--text-main)';
              btn.style.color = 'var(--bg-main)';
              window.history.replaceState({}, document.title, window.location.pathname);
          }, 3000);
      }
  };
</script>
</body>
</html>
