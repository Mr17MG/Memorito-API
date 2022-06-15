<!DOCTYPE html>
<html>

<head>

    <!-- <title> Memorito - Your working memory </title>
         
         <meta charset="UTF-8">
         <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
         <meta name="keywords" content="">
         <meta name="description" content="">
         <meta name="author" content="Mr. MG">
         
         <style>
             @import url('https://v1.fontapi.ir/css/Vazir');
             @import url('https://fonts.googleapis.com/css2?family=Roboto&display=swap');
         </style>
         
         <!-- Stylesheets -->
         <link rel="stylesheet" href="../../../../public/bootstrap/bootstrap.css">
         <link rel="stylesheet" href="../../../../public/css/misc.css">
         <link rel="stylesheet" href="../../../../public/css/blue-scheme.css">
         
         <!-- JavaScripts -->
         <script src="../../../../public/js/jquery-1.10.2.min.js"></script>
         <script src="../../../../public/js/jquery-migrate-1.2.1.min.js"></script>
         
         
         <!-- Favicon -->
         <link rel="shortcut icon" href="../../../../public/images/memorito-icon/favicon.ico" type="image/x-icon" />
         <link rel="apple-touch-icon" sizes="180x180" href="../../../../public/images/memorito-icon/apple-touch-icon.png">
         <link rel="icon" type="image/png" sizes="32x32" href="../../../../public/images/memorito-icon/favicon-32x32.png">
         <link rel="icon" type="image/png" sizes="16x16" href="../../../../public/images/memorito-icon/favicon-16x16.png">
         <link rel="manifest" href="../../../../public/images/memorito-icon/site.webmanifest)"> -->
    <style>
        .div-404 {
            display: flex;
            flex-flow: row wrap;
            align-content: center;
            justify-content: center;
            padding: 0 0 55px 0;
            border-bottom: 1px solid #dce4e6;
            text-align: center;
        }
                    
        
        .number-404 {
            width: 100%;
            text-align: center;
            background: #fff;
          position: relative;
          font: 900 30vmin "Consolas ";
          letter-spacing: 5vmin;
          text-shadow: 2px -1px 0 #000, 4px -2px 0 #0a0a0a, 6px -3px 0 #0f0f0f, 8px -4px 0 #141414, 10px -5px 0 #1a1a1a, 12px -6px 0 #1f1f1f, 14px -7px 0 #242424, 16px -8px 0 #292929;
        }
        .number-404::before {
          background-color: #673ab7;
          background-image: radial-gradient(closest-side at 50% 50%, #ffc107 100%, rgba(0, 0, 0, 0)), radial-gradient(closest-side at 50% 50%, #e91e63 100%, rgba(0, 0, 0, 0));
          background-repeat: repeat-x;
          background-size: 40vmin 40vmin;
          background-position: -100vmin 20vmin, 100vmin -25vmin;
          width: 100%;
          height: 100%;
          mix-blend-mode: screen;
          -webkit-animation: moving 10s linear infinite both;
                  animation: moving 10s linear infinite both;
          display: block;
          position: absolute;
          content: " ";
        }
        @-webkit-keyframes moving {
          to {
            background-position: 100vmin 20vmin, -100vmin -25vmin;
          }
        }
        @keyframes moving {
          to {
            background-position: 100vmin 20vmin, -100vmin -25vmin;
          }
        }
        
        .text-404 {
          font: 400 5vmin "Courgette ";
        }
        .text-404 span {
          font-size: 10vmin;
        }
        </style>
</head>

<body lang="en ">
    <!-- Header & Responsive Menu -->
    <!-- Scripts -->
    <script src="../../../../public/js/min/plugins.min.js"></script>
    <script src="../../../../public/js/min/medigo-custom.min.js"></script>
    
    
    <!-- Responsive Menu -->
    <div class="responsive_menu">
        <ul class="main_menu">
            <li lang="en"><a href="index">Home</a></li>
            <li lang="en"><a href="blog">Blog</a></li>
            <!-- <li lang="en"><a href="portfolio.html">Gallery</a></li> -->
            <li lang="fa"><a href="index">صفحه اصلی</a></li>
            <li lang="fa"><a href="blog">بلاگ</a></li>
            <!-- <li lang="fa"><a href="portfolio.html">گالری</a></li> -->
            
            <li>
                <a href="javascript:void(0);">زبان / Language</a>
                <ul>
                    <li><a href="javascript:void(0);" hreflang="fa" onclick="document.body.lang = 'fa'">فارسی</a></li>
                    <li><a href="javascript:void(0);" hreflang="en" onclick="document.body.lang = 'en'">English</a></li>
                </ul>
            </li>
            <!-- <li><a href="archives.html">Archives</a></li> -->
            <!-- <li><a href="contact.html">Contact</a></li> -->
        </ul>
        <!-- /.main_menu -->
    </div>
    <!-- /.responsive_menu -->
    
    <!-- Navbar Menu -->
    <header class="site-header clearfix">
        <div class="container">
    
            <div class="row">
    
                <div class="col-md-12">
    
                    <div class="pull-left logo">
                        <a href="index">
                            <img lang="en" src="../../../../public/images/memorito-icon/logo.png" alt="Memorito.ir">
                            <img lang="fa" src="../../../../public/images/memorito-icon/logo.png" alt="Memorito.ir">
                        </a>
                    </div>
                    <!-- /.logo -->
    
                    <div class="main-navigation pull-right">
    
                        <nav class="main-nav visible-md visible-lg">
                            <ul class="sf-menu">
    
                                <li lang="en"><a href="index">Home</a></li>
                                <li lang="en"><a href="blog">Blog</a></li>
                                <!-- <li lang="en"><a href="portfolio.html">Gallery</a></li> -->
                                <li lang="fa"><a href="index">صفحه اصلی</a></li>
                                <li lang="fa"><a href="blog">بلاگ</a></li>
                                <!-- <li lang="fa"><a href="portfolio.html">گالری</a></li> -->
                                
                                <li>
                                    <a href="javascript:void(0);">زبان / Language</a>
                                    <ul>
                                        <li><a href="javascript:void(0);" hreflang="fa" onclick="document.body.lang = 'fa'">فارسی</a></li>
                                        <li><a href="javascript:void(0);" hreflang="en" onclick="document.body.lang = 'en'">English</a></li>
                                    </ul>
                                </li>
                                <!-- <li><a href="archives.html">Archives</a></li> -->
                                <!-- <li><a href="contact.html">Contact</a></li> -->
    
                            </ul>
                            <!-- /.sf-menu -->
                        </nav>
                        <!-- /.main-nav -->
    
                        <!-- This one in here is responsive menu for tablet and mobiles -->
                        <div class="responsive-navigation visible-sm visible-xs">
                            <a href="#nogo" class="menu-toggle-btn">
                                <i class="fa fa-bars"></i>
                            </a>
                        </div>
                        <!-- /responsive_navigation -->
    
                    </div>
                    <!-- /.main-navigation -->
    
                </div>
                <!-- /.col-md-12 -->
    
            </div>
            <!-- /.row -->
    
        </div>
        <!-- /.container -->
    </header>
    <!-- /.site-header -->

    <!-- <div class=" ">
    </div> -->
    <section class="div-404 first-widget">
                    <div lang="en" class="number-404">404</div>
                    <div lang="fa" class="number-404">۴۰۴</div>
                    <div lang="en" class="text-404"><span>Ooops...</span><br>page not found</div>
                    <div lang="fa" class="text-404"><span>متاسفم</span><br>صفحه پیدا نشد</div></div> <!-- /.col-md-12 -->
    </section> <!-- /.cta -->

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <nav class="footer-nav">
                        <ul class="footer-menu">
                            <li lang="en"><a href="index">Home</a></li>
                            <li lang="en"><a href="blog">Blog Posts</a></li>
                            <!-- <li lang="en" ><a href="gallery.html">Gallery</a></li> -->
                            
                            <li lang="fa"><a href="index">صفحه اصلی</a></li>
                            <li lang="fa"><a href="blog">وبلاگ</a></li>
                            <!-- <li lang="fa" ><a href="gallery.html">گالری</a></li> -->
                            
                        </ul>
                        <!-- /.footer-menu -->
                    </nav>
                    <!-- /.footer-nav -->
                </div>
                <!-- /.col-md-12 -->
            </div>
            <!-- /.row -->
    
            <div class="row">
                <div lang="en" class="col-md-12">
                    <span class="copyright-text">Copyleft &copy;2021 Memorito</span>
                </div>
                <!-- /.col-md-12 -->
                <div lang="fa" style="text-align:center;" class="col-md-12">
                    <span class="col-md-3"></span>
                    <span class="copyright-text col-md-3">انتشار مطالب  آزاد است</span>
                    <span class="copyright-text col-md-3">&copy;2021 Memorito</span>
                    <span class="col-md-3"></span>
    
                </div>
                <!-- /.col-md-12 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </footer>
    <!-- /.site-footer -->

</body>

</html>
