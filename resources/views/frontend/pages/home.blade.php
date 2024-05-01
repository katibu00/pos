@extends('frontend.layouts.app')
@section('pageTitle','Home')
@section('content')
 <!-- Main Slider Start -->
 @include('frontend.layouts.slider')
 <!-- Main Slider End -->

 <!-- About Start -->
 <section class="section ec-about-sec section-space-p">
     <div class="container">
         <div class="row">
             <div class="section-title d-none">
                 <h2 class="ec-title">About</h2>
             </div>
             <div class="col-lg-6">
                 <div class="ec-about">
                     <img src="/frontend/images/about/about-10.png" alt="about-image">
                 </div>
             </div>
             <div class="col-lg-6">
                 <div class="ec-about-detail">
                     <h4>Bring power to your Work, with incredible tools.</h4>
                     <h5>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested alteration in some form.</h5>
                     <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                     <p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from in Virginia.</p>
                     <a class="btn btn-lg btn-primary" href="shop-banner-left-sidebar-col-3.html">Buy Now</a>
                 </div>
             </div>
         </div>
     </div>
 </section>
 <!-- About End -->

 <!--  category Section Start -->
 <section class="section ec-category-section section-space-p">
     <div class="container">
         <div class="row">
             <div class="col-md-12 section-title-block">
                 <div class="section-title">
                     <h2 class="ec-title">Browse By Categories</h2>
                     <p class="sub-title">Check out our feature products.</p>
                 </div>
                 <div class="section-btn">
                     <span class="ec-section-btn"><a class="btn-primary" href="shop-left-sidebar-col-3.html">View All Categories</a></span>
                 </div>
             </div>
         </div>
         <div class="row">
             <div class="ec_cat_slider">



                 @forelse ($categories as $category)
                 <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6 ec_cat_content">
                     <div class="ec_cat_inner">
                         <div class="ec-cat-image">
                             <img class="category-image" src="{{ asset($category->image) }}" alt="{{ $category->name }}" />
                         </div>
                         <div class="ec-cat-desc">
                             <span class="ec-section-btn"><a href="shop-left-sidebar-col-3.html" class="btn-primary">{{ $category->name }}</a></span>
                         </div>
                     </div>
                 </div>
             @empty
                 <p>No categories available yet.</p>
             @endforelse
             
                
             </div>
         </div>
     </div>
 </section>
 <!--category Section End -->

 <!-- ec Banner Section Start -->
 <section class="ec-banner section section-space-p">
     <div class="container">
         <h2 class="d-none">Banner</h2>
         <div class="ec-banners">
             <div class="ec-banner-left col-sm-6">
                 <div class="ec-banner-block ec-banner-block-1 col-sm-12">
                     <div class="banner-block">
                         <img src="/frontend/images/banner/28.jpg" alt="" />
                         <div class="banner-content">
                             <div class="banner-text">
                                 <span class="ec-banner-stitle">20% Off ! Limited week deal</span>
                                 <span class="ec-banner-title">tools box</span>
                                 <p>Lorem ipsum, or lipsum as it <br>is sometimes known, is dummy <br>since 1991.</p>
                             </div>
                             <span class="ec-banner-btn"><a href="shop-left-sidebar-col-3.html" class="btn-primary">Discover Now</a></span>
                         </div>
                     </div>
                 </div>
             </div>
             <div class="ec-banner-right col-sm-6">
                 <div class="ec-banner-block ec-banner-block-2 col-sm-12">
                     <div class="banner-block">
                         <img src="/frontend/images/banner/29.jpg" alt="" />
                         <div class="banner-content">
                             <div class="banner-text">
                                 <span class="ec-banner-stitle">Rezer’s</span>
                                 <span class="ec-banner-title">welding machine</span>
                             </div>
                             <span class="ec-banner-btn"><a href="shop-left-sidebar-col-3.html" class="btn-primary">Discover Now</a></span>
                         </div>
                     </div>
                 </div>
                 <div class="ec-banner-block ec-banner-block-3 col-sm-6">
                     <div class="banner-block">
                         <a href="shop-left-sidebar-col-3.html">
                             <img src="/frontend/images/banner/30.jpg" alt="" />
                             <div class="banner-content">
                                 <div class="banner-text">
                                     <span class="ec-banner-stitle">Exlusive</span>
                                     <span class="ec-banner-title">cleaner machine</span>
                                     <span class="ec-banner-desc">Starting <span>$500.00</span></span>
                                 </div>
                             </div>
                         </a>
                     </div>
                 </div>
                 <div class="ec-banner-block ec-banner-block-4 col-sm-6">
                     <div class="banner-block">
                         <a href="shop-left-sidebar-col-3.html">
                             <img src="/frontend/images/banner/31.jpg" alt="" />
                             <div class="banner-content">
                                 <div class="banner-text">
                                     <span class="ec-banner-stitle">Best 2022 Gear</span>
                                     <span class="ec-banner-title">level machine</span>
                                 </div>
                             </div>
                         </a>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </section>
 <!-- ec Banner Section End -->

 <!-- Product tab Area Start -->
 <section class="section ec-product-tab section-space-p">
     <div class="container">
         <div class="row">
             <div class="col-md-12 section-title-block">
                 <div class="section-title">
                     <h2 class="ec-title">Exclusive Products</h2>
                     <p class="sub-title">Check out our exclusive products.</p>
                 </div>
                 <div class="section-btn">
                     <ul class="ec-pro-tab-nav nav">
                         <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab"
                                 href="#tab-pro-new-arrivals">New Arrivals</a></li>
                         <li class="nav-item"><a class="nav-link" data-bs-toggle="tab"
                                 href="#tab-pro-special-offer">Special Offer</a></li>
                         <li class="nav-item"><a class="nav-link" data-bs-toggle="tab"
                                 href="#tab-pro-best-sellers">Best Sellers</a></li>
                     </ul>
                 </div>
             </div>

         </div>
         <div class="row">
             <div class="col">
                 <div class="tab-content">
                     <!-- 1st Product tab start -->
                     <div class="tab-pane fade show active" id="tab-pro-new-arrivals">
                         <div class="row">
                             <div class="ec-pro-tab-slider">
                                
                                @foreach ($products as $product)
                                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                        <div class="ec-product-inner">
                                            <div class="ec-pro-image-outer">
                                                <div class="ec-pro-image">
                                                    <a href="product-left-sidebar.html" class="image">
                                                        <img class="main-image" src="{{ asset($product->onlineProductImages->where('featured', true)->first()->image_url ?? 'default-image.jpg') }}" alt="Product" />
                                                        <img class="hover-image" src="{{ asset($product->onlineProductImages->skip(1)->first()->image_url ?? $product->onlineProductImages->first()->image_url) }}" alt="Product" />
                                                    </a>
                                                    @if ($product->discount_applied)
                                                        <span class="flags">
                                                            <span class="percentage">-{{ number_format($product->discount_price * 100 / $product->original_price, 2) }}%</span>
                                                        </span>
                                                    @endif
                                                    <div class="ec-pro-actions">
                                                        <button title="Add To Cart" class="add-to-cart"><i class="fi-rr-shopping-basket"></i></button>
                                                        <a href="#" class="ec-btn-group quickview" title="Quick view" data-bs-toggle="modal" data-bs-target="#ec_quickview_modal" onclick="fetchProductDetails({{ $product->id }})"><i class="fi-rr-eye"></i></a>
                                                        <a class="ec-btn-group wishlist" title="Wishlist"><i class="fi-rr-heart"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ec-pro-content">
                                                <h5 class="ec-pro-title"><a href="product-left-sidebar.html">{{ $product->product->name }}</a></h5>
                                                <span class="ec-price">
                                                    @if ($product->discount_applied)
                                                        <span class="old-price">$ {{ number_format($product->original_price, 2) }}</span>
                                                    @endif
                                                    <span class="new-price">$ {{ number_format($product->selling_price, 2) }}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                
                             </div>
                         </div>
                     </div>
                     <!-- ec 1st Product tab end -->
                     <!-- ec 2nd Product tab start -->
                     <div class="tab-pane fade" id="tab-pro-special-offer">
                         <div class="row">
                             <div class="ec-pro-tab-slider">
                               

                                
                             </div>
                         </div>
                     </div>
                     <!-- ec 2nd Product tab end -->
                     <!-- ec 3rd Product tab start -->
                     <div class="tab-pane fade" id="tab-pro-best-sellers">
                         <div class="row">
                             <div class="ec-pro-tab-slider">
                                

                             </div>
                         </div>
                     </div>
                     <!-- ec 3rd Product tab end -->
                 </div>
             </div>
         </div>
     </div>
 </section>
 <!-- ec Product tab Area End -->

 <!--  offer Section Start -->
 <section class="section ec-offer-section section-space-mt section-space-mb">
     <h2 class="d-none">Offer</h2>
     <div class="container">
         <div class="ec-banner-info-10">
                 <div class="ec-offer-inner">
                     <div class="col-sm-4 ec-offer-content">
                         <h2 class="ec-offer-stitle">Happy Sunday</h2>
                         <h2 class="ec-offer-title">40% off</h2>
                         <span class="ec-offer-desc">All Construction Tools</span>
                         <div class="countdowntimer"><span id="ec-offer-count"></span></div>
                         <span class="ec-offer-btn"><a href="#" class="btn btn-lg btn-secondary">Shop Now</a></span>
                     </div>
                 </div>
         </div>
     </div>
 </section>
 <!-- offer Section End -->

 <!-- Trending Item Start -->
 <section class="section ec-trend-product section-space-p">
     <div class="container">
         <div class="row">
             <div class="col-md-12 section-title-block">
                 <div class="section-title">
                     <h2 class="ec-title">Trending Item</h2>
                     <p class="sub-title">Check out our trending products.</p>
                 </div>
                 <div class="section-btn">
                     <span class="ec-section-btn"><a class="btn-secondary" href="shop-left-sidebar-col-3.html">View All</a></span>
                 </div>
             </div>
         </div>
         <div class="row">
             <div class="ec-trend-slider">
              
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                     <div class="ec-product-inner">
                         <div class="ec-pro-image-outer">
                             <div class="ec-pro-image">
                                 <a href="product-left-sidebar.html" class="image">
                                     <img class="main-image"
                                         src="/frontend/images/product-image/137_2.jpg"
                                         alt="Product" />
                                     <img class="hover-image"
                                         src="/frontend/images/product-image/137_1.jpg"
                                         alt="Product" />
                                 </a>
                                 <div class="ec-pro-actions">
                                     <button title="Add To Cart" class=" add-to-cart"><i class="fi-rr-shopping-basket"></i></button>
                                     <a href="compare.html" class="ec-btn-group compare" title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                     <a href="#" class="ec-btn-group quickview" data-link-action="quickview" title="Quick view"
                                         data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i class="fi-rr-eye"></i></a>
                                     <a class="ec-btn-group wishlist" title="Wishlist"><i class="fi-rr-heart"></i></a>
                                 </div>
                             </div>
                         </div>
                         <div class="ec-pro-content">
                             <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Vice tool for wooden work</a>
                             </h5>
                             <span class="ec-price">
                                 <span class="old-price">$450</span>
                                 <span class="new-price">$370</span>
                             </span>
                         </div>
                     </div>
                 </div>
                 
             </div>
         </div>
     </div>
 </section>
 <!-- Trending Item end -->

 <!--  services Section Start -->
 <section class="section ec-services-section section-space-p">
     <h2 class="d-none">Services</h2>
     <div class="container">
         <div class="row mb-minus-30">
             
             <div class="ec_ser_content ec_ser_content_1 col-sm-12 col-md-3">
                 <div class="ec_ser_inner">
                     <div class="ec-service-image">
                         <i class="fi fi-ts-tachometer-fast"></i>
                     </div>
                     <div class="ec-service-desc">
                         <h2>Quick Delivery</h2>
                         <p>For Order Over $100.</p>
                     </div>
                 </div>
             </div>
             <div class="ec_ser_content ec_ser_content_2 col-sm-12 col-md-3">
                 <div class="ec_ser_inner">
                     <div class="ec-service-image">
                         <i class="fi fi-ts-truck-moving"></i>
                     </div>
                     <div class="ec-service-desc">
                         <h2>Free Returns</h2>
                         <p>Easy & Free Return.</p>
                     </div>
                 </div>
             </div>
             <div class="ec_ser_content ec_ser_content_3 col-sm-12 col-md-3">
                 <div class="ec_ser_inner">
                     <div class="ec-service-image">
                         <i class="fi fi-ts-circle-phone"></i>
                     </div>
                     <div class="ec-service-desc">
                         <h2>24/7 Support</h2>
                         <p>Free Online Support.</p>
                     </div>
                 </div>
             </div>
             <div class="ec_ser_content ec_ser_content_3 col-sm-12 col-md-3">
                 <div class="ec_ser_inner">
                     <div class="ec-service-image">
                         <i class="fi fi-ts-donate"></i>
                     </div>
                     <div class="ec-service-desc">
                         <h2>Secure Payment</h2>
                         <p>Refund Guaranteed.</p>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </section>
 <!--services Section End -->

 <!-- ec testimonial Start -->
 <section class="section ec-test-section section-space-p">
     <div class="container">
         <div class="row">
             <div class="col-md-12 section-title-block">
                 <div class="section-title">
                     <h2 class="ec-title">Testimonial</h2>
                     <p class="sub-title">What our clients say about us!</p>
                 </div>
             </div>
         </div>
         <div class="row">
             <div class="ec-test-outer">
                 <ul id="ec-testimonial-slider">
                     <li class="ec-test-item">
                         <div class="ec-test-inner">
                             <div class="ec-test-img"><img alt="testimonial" title="testimonial"
                                     src="/frontend/images/testimonial/1.jpg" /></div>
                             <div class="ec-test-content">
                                 <div class="ec-test-icon"><i class="fi-rr-quote-right"></i></div>
                                 <div class="ec-test-desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                                     sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad
                                     minim veniam.</div>
                                 <div class="ec-test-name-info"> 
                                     <div class="ec-test-name">Jenifer Brown</div>
                                     <div class="ec-test-designation">Manager of AZ</div>
                                 </div>
                             </div>
                         </div>
                     </li>
                     <li class="ec-test-item">
                         <div class="ec-test-inner">
                             <div class="ec-test-img"><img alt="testimonial" title="testimonial"
                                     src="/frontend/images/testimonial/2.jpg" /></div>
                             <div class="ec-test-content">
                                 <div class="ec-test-icon"><i class="fi-rr-quote-right"></i></div>
                                 <div class="ec-test-desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                                     sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad
                                     minim veniam.</div>
                                     <div class="ec-test-name-info">
                                         <div class="ec-test-name">Maria Zukerbug</div>
                                         <div class="ec-test-designation">Manager of AZ</div>
                                     </div>
                             </div>
                         </div>
                     </li>
                     <li class="ec-test-item">
                         <div class="ec-test-inner">
                             <div class="ec-test-img"><img alt="testimonial" title="testimonial"
                                     src="/frontend/images/testimonial/3.jpg" /></div>
                             <div class="ec-test-content">
                                 <div class="ec-test-icon"><i class="fi-rr-quote-right"></i></div>
                                 <div class="ec-test-desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                                     sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad
                                     minim veniam.</div>
                                     <div class="ec-test-name-info">
                                         <div class="ec-test-name">Moris Selemen</div>
                                         <div class="ec-test-designation">Manager of AZ</div>
                                     </div>
                             </div>
                         </div>
                     </li>
                 </ul>
             </div>
         </div>
     </div>
 </section>
 <!-- ec testimonial end -->

 @endsection

 @section('js')
 {{-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> --}}

 <script>
    function fetchProductDetails(productId) {
    // Target the modal by its id
    var modalBody = $('#modal_body_ec_quickview_modal');
    var spinner = $('#spinner_ec_quickview_modal');

    $.ajax({
        url: '/get-product-details/' + productId,
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            // Show loading spinner inside the modal
            spinner.show();
        },
        success: function(response) {
    // Hide loading spinner
    spinner.hide();

    // Render product details
    var product = response.product;
    var html = '<div class="row">';
    // Product images
    html += '<div class="col-md-5 col-sm-12 col-xs-12">';
    html += '<div class="qty-product-cover">';
    $.each(product.onlineProductImages, function(index, image) {
        html += '<div class="qty-slide">';
        html += '<img class="img-responsive" src="' + image.image_url + '" alt="Product Image">';
        html += '</div>';
    });
    html += '</div>';
    html += '<div class="qty-nav-thumb">';
    $.each(product.onlineProductImages, function(index, image) {
        html += '<div class="qty-slide">';
        html += '<img class="img-responsive" src="/' + image.image_url + '" alt="Product Image">';
        html += '</div>';
    });
    html += '</div>';
    html += '</div>';
    // Product details
    html += '<div class="col-md-7 col-sm-12 col-xs-12">';
    html += '<div class="quickview-pro-content">';
    html += '<h5 class="ec-quick-title"><a href="product-left-sidebar.html">' + product.product.name + '</a></h5>';
    html += '<div class="ec-quickview-rating">';
    // Render product rating
    // For example: html += '<i class="ecicon eci-star fill"></i>';
    html += '</div>';
    html += '<div class="ec-quickview-desc">' + product.description + '</div>';
    html += '<div class="ec-quickview-price">';
    html += '<span class="old-price">$' + product.original_price + '</span>';
    html += '<span class="new-price">$' + product.selling_price + '</span>';
    html += '</div>';
    // Render product variations
    // For example: html += '<div class="ec-pro-variation">...</div>';
    html += '</div>';
    html += '</div>';
    html += '</div>';

    // Append HTML to modal body
    modalBody.html(html);
},

        error: function(xhr, status, error) {
            // Handle errors
        }
    });
}

 </script>

 
 @endsection