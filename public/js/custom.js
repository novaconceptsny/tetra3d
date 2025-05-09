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
            $(".nav__link .nav__text").addClass("hide");

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
            $(".nav__link .nav__text").removeClass("hide");

            $("#nav__search__icon").click(function () {
                $("#search__box").slideToggle();
            });
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
        $(this).parent().toggleClass("show h-100");
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
        $(this).parent().parent().parent().next().slideToggle();
        $(this).siblings(".arrow").toggleClass("rotate");

        if ($(".accordion__item .arrow.rotate").hasClass("rotate")) {
            $(".accordion__item .arrow.rotate")
                .parent()
                .parent()
                .parent()
                .parent()
                .addClass("active");
        }
    });

    $(".accordion__item .arrow").click(function () {
        $(".accordion__item").removeClass("active");
        $(this).parent().parent().parent().next().slideToggle();
        $(this).toggleClass("rotate");

        if ($(".accordion__item .arrow.rotate").hasClass("rotate")) {
            $(".accordion__item .arrow.rotate")
                .parent()
                .parent()
                .parent()
                .parent()
                .addClass("active");
        }
    });

    $(".sidebar .nav__item").click(function () {
        $(".sidebar .nav__item").removeClass("active");
        $(this).addClass("active");
    });
});
