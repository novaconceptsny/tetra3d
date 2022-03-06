$(document).ready(function () {
  function reportWindow(width) {
    if (width >= 992) {
      $(".sidebar").removeClass("hide");
      $(".dashboard").removeClass("wide");
      $("#search__box").css("display", "none");
      $(".main__header.universal").removeClass("hide");
      $(".universal__wrapper .dashboard").removeClass("wide");
      $(".collections").removeClass("hide");
      $(".footer__pagination").removeClass("hide");

      $("#expand__menu").click(function () {
        $(this).children(".icon").toggleClass("rotate");
        $(".nav__link .nav__text").toggleClass("hide");
        $(".sidebar").toggleClass("mini");
        $(".sidebar .navbar__nav").toggleClass("mini");
        $(".sidebar .brand").toggleClass("mini");
        $(".main__header.universal").toggleClass("mini");
        $(".dashboard").toggleClass("mini");
        $(".collections").toggleClass("mini");
        $(".footer__pagination").toggleClass("mini");
      });
    }

    if (width < 992) {
      $(".sidebar").addClass("hide");
      $(".dashboard").addClass("wide");
      $(".main__header.universal").addClass("hide");
      $(".collections").addClass("hide");
      $(".universal__wrapper .dashboard").addClass("wide");
      $(".footer__pagination").addClass("hide");

      $("#nav__search__icon").click(function () {
        $("#search__box").slideToggle();
      });
      $(".nav__link .nav__text").removeClass("hide");
    }
  }
  reportWindow($(window).width());

  $(window).resize(function () {
    reportWindow($(window).width());
  });

  $(".main__header .menu__btn").click(function () {
    $(".sidebar").toggleClass("hide");
    $(".collections").toggleClass("hide");
    $(".footer__pagination").toggleClass("hide");
  });

  $(".collections .card-header").click(function () {
    $(this).parent().toggleClass("show");
    if ($(window).width() < 992) {
      $(".sidebar").addClass("hide");
      $(this).parent().addClass("hide");
    }
  });

  $(".sidebar__trigger").click(function () {
    $(".surface__sidebar").removeClass("show");
  });

  $(".sidebar__trigger.fixed").click(function () {
    $(".surface__sidebar").addClass("show");
  });

  $(".accordion__trigger").click(function () {
    $(".accordion__item").removeClass("active");
    $(this).parent().parent().next().slideToggle();
    $(this).siblings(".arrow").toggleClass("rotate");

    if ($(".accordion__item .arrow.rotate").hasClass("rotate")) {
      console.log(
        $(".accordion__item .arrow.rotate")
          .parent()
          .parent()
          .parent()
          .addClass("active")
      );
      // $(".accordion__item .arrow.rotate").parent().parent().addClass("active");
    }
  });

  $('.arrow').click(function(){
    $(this).toggleClass('rotate');
  })

 
  $('.dot_1').click(function(){
    $('.drop_1 ul').toggleClass('active');
  });
  $('.dot_2').click(function(){
    $('.drop_2 ul').toggleClass('active');
  });
  $('.dot_3').click(function(){
    $('.drop_3 ul').toggleClass('active');
  });
  $('.dot_4').click(function(){
    $('.drop_4 ul').toggleClass('active');
  });

  $('.dot_5').click(function(){
    $('.drop_5 ul').toggleClass('active');
  });
  $('.dot_6').click(function(){
    $('.drop_6 ul').toggleClass('active');
  });
  $('.dot_7').click(function(){
    $('.drop_7 ul').toggleClass('active');
  });
  $('.dot_8').click(function(){
    $('.drop_8 ul').toggleClass('active');
  });


  $('.dot_9').click(function(){
    $('.drop_9 ul').toggleClass('active');
  });
  $('.dot_10').click(function(){
    $('.drop_10 ul').toggleClass('active');
  });
  $('.dot_11').click(function(){
    $('.drop_11 ul').toggleClass('active');
  });
  $('.dot_12').click(function(){
    $('.drop_12 ul').toggleClass('active');
  });

  $('.dot_13').click(function(){
    $('.drop_13 ul').toggleClass('active');
  });
  $('.dot_14').click(function(){
    $('.drop_14 ul').toggleClass('active');
  });
  $('.dot_15').click(function(){
    $('.drop_15 ul').toggleClass('active');
  });

  $(".accordion__item .arrow").click(function () {
    $(".accordion__item").removeClass("active");
    $(this).parent().parent().next().slideToggle();
    $(this).siblings(".arrow").toggleClass("rotate");

    if ($(".accordion__item .arrow.rotate").hasClass("rotate")) {
      console.log(
        $(".accordion__item .arrow.rotate")
          .parent()
          .parent()
          .parent()
          .addClass("active")
      );
    }
  });

  $(".sidebar .nav__item").click(function () {
    $(".sidebar .nav__item").removeClass("active");
    $(this).addClass("active");
  });
});

