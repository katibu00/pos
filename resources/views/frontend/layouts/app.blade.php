<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from maraviyainfotech.com/projects/ekka/ekka-v37/ekka-html/demo-10.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 02 Apr 2024 13:07:12 GMT -->
<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">

    <title>@yield('pageTitle') - El-Habib Plumbing Materials and Services</title>

    <meta name="author" content="Umar Katibu">
    
   <!-- site Favicon -->
   <link rel="icon" href="/frontend/images/favicon/favicon-10.png" sizes="32x32" />
   <link rel="apple-touch-icon" href="/frontend/images/favicon/favicon-10.png" />
   <meta name="msapplication-TileImage" content="assets/images/favicon/favicon-10.png" />

   <!-- css Icon Font -->
   <link rel="stylesheet" href="/frontend/css/vendor/ecicons.min.css" />

   <!-- css All Plugins Files -->
   <link rel="stylesheet" href="/frontend/css/plugins/animate.css" />
   <link rel="stylesheet" href="/frontend/css/plugins/swiper-bundle.min.css" />
   <link rel="stylesheet" href="/frontend/css/plugins/jquery-ui.min.css" />
   <link rel="stylesheet" href="/frontend/css/plugins/countdownTimer.css" />
   <link rel="stylesheet" href="/frontend/css/plugins/slick.min.css" />
   <link rel="stylesheet" href="/frontend/css/plugins/bootstrap.css" />

   <!-- Main Style -->
   <link rel="stylesheet" href="/frontend/css/demo10.css" />
   
</head>
<body>
   <div id="ec-overlay">
       <div class="ec-ellipsis">
           <div></div>
           <div></div>
           <div></div>
           <div></div>
       </div>
   </div>

   <!-- Header start  -->
    @include('frontend.layouts.header')
   <!-- Header End  -->

   <!-- Ekka Cart Start -->
   <div class="ec-side-cart-overlay"></div>
   <div id="ec-side-cart" class="ec-side-cart">
       <div class="ec-cart-inner">
           <div class="ec-cart-top">
               <div class="ec-cart-title">
                   <span class="cart_title">My Cart</span>
                   <button class="ec-close">×</button>
               </div>
               <ul class="eccart-pro-items">
                   <li>
                       <a href="product-left-sidebar.html" class="sidecart_pro_img"><img
                               src="/frontend/images/product-image/130_1.jpg" alt="product"></a>
                       <div class="ec-pro-content">
                           <a href="single-product-left-sidebar.html" class="cart_pro_title">Breaker machine smart tech</a>
                           <span class="cart-price"><span>$799</span> x 1</span>
                           <div class="qty-plus-minus">
                               <input class="qty-input" type="text" name="ec_qtybtn" value="1" />
                           </div>
                           <a href="#" class="remove">×</a>
                       </div>
                   </li>
                   <li>
                       <a href="product-left-sidebar.html" class="sidecart_pro_img"><img
                               src="/frontend/images/product-image/131_1.jpg" alt="product"></a>
                       <div class="ec-pro-content">
                           <a href="product-left-sidebar.html" class="cart_pro_title">Ceramic tiles cutter</a>
                           <span class="cart-price"><span>$400</span> x 1</span>
                           <div class="qty-plus-minus">
                               <input class="qty-input" type="text" name="ec_qtybtn" value="1" />
                           </div>
                           <a href="#" class="remove">×</a>
                       </div>
                   </li>
                   <li>
                       <a href="product-left-sidebar.html" class="sidecart_pro_img"><img
                               src="/frontend/images/product-image/132_1.jpg" alt="product"></a>
                       <div class="ec-pro-content">
                           <a href="product-left-sidebar.html" class="cart_pro_title">Small chainsaw for wood cutting</a>
                           <span class="cart-price"><span>$3500</span> x 1</span>
                           <div class="qty-plus-minus">
                               <input class="qty-input" type="text" name="ec_qtybtn" value="1" />
                           </div>
                           <a href="#" class="remove">×</a>
                       </div>
                   </li>
               </ul>
           </div>
           <div class="ec-cart-bottom">
               <div class="cart-sub-total">
                   <table class="table cart-table">
                       <tbody>
                           <tr>
                               <td class="text-left">Sub-Total :</td>
                               <td class="text-right">$1350.00</td>
                           </tr>
                           <tr>
                               <td class="text-left">VAT (20%) :</td>
                               <td class="text-right">$270.00</td>
                           </tr>
                           <tr>
                               <td class="text-left">Total :</td>
                               <td class="text-right primary-color">$1620.00</td>
                           </tr>
                       </tbody>
                   </table>
               </div>
               <div class="cart_btn">
                   <a href="cart.html" class="btn btn-primary">View Cart</a>
                   <a href="checkout.html" class="btn btn-secondary">Checkout</a>
               </div>
           </div>
       </div>
   </div>
   <!-- Ekka Cart End -->

   @yield('content')

   <!-- Footer Start -->
   @include('frontend.layouts.footer')
   <!-- Footer Area End -->

   <!-- Modal -->
   <div class="modal fade" id="ec_quickview_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close qty_close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body" id="modal_body_ec_quickview_modal">
                <div class="d-flex justify-content-center align-items-center" style="height: 200px;">
                    <div class="spinner-border text-primary" role="status" id="spinner_ec_quickview_modal">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <!-- Add other modal content here -->
            </div>
        </div>
     </div>
    </div>


   <!-- Modal end -->
   <!-- Modal -->
   {{-- <div class="modal fade" id="ec_quickview_modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-dialog-centered" role="document">
           <div class="modal-content">
               <button type="button" class="btn-close qty_close" data-bs-dismiss="modal" aria-label="Close"></button>
               <div class="modal-body">
                   <div class="row">
                       <div class="col-md-5 col-sm-12 col-xs-12">
                           <!-- Swiper -->
                           <div class="qty-product-cover">
                               <div class="qty-slide">
                                   <img class="img-responsive" src="/frontend/images/product-image/19_1.jpg"
                                       alt="">
                               </div>
                               <div class="qty-slide">
                                   <img class="img-responsive" src="/frontend/images/product-image/19_2.jpg"
                                       alt="">
                               </div>
                               <div class="qty-slide">
                                   <img class="img-responsive" src="/frontend/images/product-image/27_1.jpg"
                                       alt="">
                               </div>
                               <div class="qty-slide">
                                   <img class="img-responsive" src="/frontend/images/product-image/27_2.jpg"
                                       alt="">
                               </div>
                           </div>
                           <div class="qty-nav-thumb">
                               <div class="qty-slide">
                                   <img class="img-responsive" src="/frontend/images/product-image/19_1.jpg"
                                       alt="">
                               </div>
                               <div class="qty-slide">
                                   <img class="img-responsive" src="/frontend/images/product-image/19_2.jpg"
                                       alt="">
                               </div>
                               <div class="qty-slide">
                                   <img class="img-responsive" src="/frontend/images/product-image/27_1.jpg"
                                       alt="">
                               </div>
                               <div class="qty-slide">
                                   <img class="img-responsive" src="/frontend/images/product-image/27_2.jpg"
                                       alt="">
                               </div>
                           </div>
                       </div>
                       <div class="col-md-7 col-sm-12 col-xs-12">
                           <div class="quickview-pro-content">
                               <h5 class="ec-quick-title"><a href="product-left-sidebar.html">Single sofa for living room</a></h5>
                               <div class="ec-quickview-rating">
                                   <i class="ecicon eci-star fill"></i>
                                   <i class="ecicon eci-star fill"></i>
                                   <i class="ecicon eci-star fill"></i>
                                   <i class="ecicon eci-star fill"></i>
                                   <i class="ecicon eci-star"></i>
                               </div>
                               <div class="ec-quickview-desc">Lorem Ipsum is simply dummy text of the printing and
                                   typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever
                                   since the 1500s,</div>
                               <div class="ec-quickview-price">
                                   <span class="old-price">$450.00</span>
                                   <span class="new-price">$400.00</span>
                               </div>

                               <div class="ec-pro-variation">
                                   <div class="ec-pro-variation-inner ec-pro-variation-color">
                                       <span>Color</span>
                                       <div class="ec-pro-variation-content">
                                           <ul>
                                               <li><span style="background-color:#696d62;"></span></li>
                                               <li><span style="background-color:#2ea1cd;"></span></li>
                                           </ul>
                                       </div>
                                   </div>
                               </div>
                               <div class="ec-quickview-qty">
                                   <div class="qty-plus-minus">
                                       <input class="qty-input" type="text" name="ec_qtybtn" value="1" />
                                   </div>
                                   <div class="ec-quickview-cart ">
                                       <button class="btn btn-secondary"><i class="fi-rr-shopping-basket"></i> Add To Cart</button>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div> --}}
   <!-- Modal end -->

   <!-- FB Chat Messenger -->
   <div class="ec-fb-style fb-right-bottom">

       <!-- Start Floating Panel Container -->
       <div class="fb-panel">
           <!-- Panel Content -->
           <div class="fb-header">
               <img src="/frontend/images/user/1.jpg" alt="pic" />
               <h2>Maria Mark</h2>
               <p>Technical Manager</p>
           </div>
           <div class="fb-body">
               <p><b>Hey there &#128515;</b></p>
               <p>Need help ? just send me a message.</p>
           </div>
           <div class="fb-footer">

               <a href="http://m.me/Loopinfosol" target="_blank" class="fb-msg-button">
                   <span>Send Message</span>
                   <svg width="13px" height="10px" viewBox="0 0 13 10">
                       <path d="M1,5 L11,5"></path>
                       <polyline points="8 1 12 5 8 9"></polyline>
                   </svg>
               </a>

           </div>
       </div>
       <!--/ End Floating Panel Container -->

       <!-- Start Right Floating Button -->
       <div class="fb-button fb-right-bottom">
           <i class="fi-rr-phone-call"></i>
       </div>
       <!--/ End Right Floating Button -->

   </div>
   <!-- FB Chat Messenger end -->

   <!-- Newsletter Modal Start -->
   <div id="ec-popnews-bg"></div>
   <div id="ec-popnews-box">
       <div id="ec-popnews-close"><i class="ecicon eci-close"></i></div>
       <div id="ec-popnews-box-content">
           <h1>Subscribe Newsletter</h1>
           <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
           <form id="ec-popnews-form" action="#" method="post">
               <input type="email" name="newsemail" placeholder="Email Address" required />
               <button type="button" class="btn btn-secondary" name="subscribe">Subscribe</button>
           </form>
       </div>
   </div>
   <!-- Newsletter Modal end -->

   <!-- successfully toast Start -->
   <div id="addtocart_toast" class="addtocart_toast">
       <div class="desc">You Have Add To Cart Successfully</div>
   </div>
   <div id="wishlist_toast" class="wishlist_toast">
       <div class="desc">You Have Add To Wishlist Successfully</div>
   </div>
   <!--successfully toast end -->

   <!-- Theme Custom Cursors -->
   <div class="ec-cursor"></div>
   <div class="ec-cursor-2"></div>

   <!-- Vendor JS -->
   <script src="/frontend/js/vendor/jquery-3.5.1.min.js"></script>
   <script src="/frontend/js/vendor/popper.min.js"></script>
   <script src="/frontend/js/vendor/bootstrap.min.js"></script>
   <script src="/frontend/js/vendor/jquery-migrate-3.3.0.min.js"></script>
   <script src="/frontend/js/vendor/modernizr-3.11.2.min.js"></script>

   <!--Plugins JS-->
   <script src="/frontend/js/plugins/swiper-bundle.min.js"></script>
   <script src="/frontend/js/plugins/countdownTimer.min.js"></script>
   <script src="/frontend/js/plugins/scrollup.js"></script>
   <script src="/frontend/js/plugins/jquery.zoom.min.js"></script>
   <script src="/frontend/js/plugins/slick.min.js"></script>
   <script src="/frontend/js/plugins/infiniteslidev2.js"></script>
   <script src="/frontend/js/plugins/fb-chat.js"></script>

   <!-- Main Js -->
   <script src="/frontend/js/vendor/index.js"></script>
   <script src="/frontend/js/demo-10.js"></script>
   @yield('js')
</body>

</html>