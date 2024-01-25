// Template Name: Thunder Pro
// Description: Thunder Pro Weather Forcast And News Template
// Version: 1.0.0
(function (window, document, $, undefined) {
  "use strict";
  var Init = {
    i: function (e) {
      Init.s();
      Init.methods();
    },
    s: function (e) {
      (this._window = $(window)),
        (this._document = $(document)),
        (this._body = $("body")),
        (this._html = $("html"));
    },
    methods: function (e) {
      Init.w();
      Init.BackToTop();
      Init.preloader();
      Init.colorMode();
      Init.intializeSlick();
      Init.temperature();
      Init.formValidation();
      Init.contactForm();
      Init.videoPlay();
      Init.map();
      Init.chartJs();
    },
    w: function (e) {
      this._window.on("load", Init.l).on("scroll", Init.res);
    },
    BackToTop: function () {
      var btn = $("#backto-top");
      $(window).on("scroll", function () {
        if ($(window).scrollTop() > 300) {
          btn.addClass("show");
        } else {
          btn.removeClass("show");
        }
      });
      btn.on("click", function (e) {
        e.preventDefault();
        $("html, body").animate(
          {
            scrollTop: 0,
          },
          "300"
        );
      });
    },
    preloader: function () {
      setTimeout(function () { $('#preloader').hide('slow') }, 2000);
    },
    colorMode: function () {
      $('#changeColor').on('change', function () {

        if ($('body').hasClass('dark')) {
          $('body').removeClass('dark');
        } else {
          $('body').addClass('dark');
        }
      })
    },
    intializeSlick: function (e) {

      if ($(".hourly-slider").length) {
        var slider = $('.hourly-slider').slick({
          infinite: true,
          centerMode: false,
          arrows: true,
          centerPadding: '',
          slidesToShow: 7,
          responsive: [
            {
              breakpoint: 1299,
              settings: {

                slidesToShow: 6,
              },
            },
            {
              breakpoint: 1199,
              settings: {

                slidesToShow: 5,
              },
            },
            {
              breakpoint: 992,
              settings: {

                slidesToShow: 4,
              },
            },
            {
              breakpoint: 768,
              settings: {
                slidesToShow: 3,
              },
            },
            {
              breakpoint: 580,
              settings: {
                slidesToShow: 2,
                arrows: false,
              },
            },
            {
              breakpoint: 490,
              settings: {
                slidesToShow: 1,
                arrows: false,
              },
            },
          ],
        });
        slider.on('wheel', (function (e) {
          e.preventDefault();

          if (e.originalEvent.deltaY < 0) {
            $(this).slick('slickPrev');
          } else {
            $(this).slick('slickNext');
          }
        }));
      }
      if ($(".recent-slider").length) {
        var slider = $('.recent-slider').slick({
          infinite: true,
          centerMode: true,
          arrows: true,
          centerPadding: '0px',
          slidesToShow: 4,
          responsive: [
            {
              breakpoint: 1199,
              settings: {
                slidesToShow: 3,
              },
            },
            {
              breakpoint: 992,
              settings: {
                slidesToShow: 2,
              },
            },
            {
              breakpoint: 768,
              settings: {
                slidesToShow: 1,
                arrows: false,
              },
            }
          ],
        });
        slider.on('wheel', (function (e) {
          e.preventDefault();

          if (e.originalEvent.deltaY < 0) {
            $(this).slick('slickPrev');
          } else {
            $(this).slick('slickNext');
          }
        }));
      }
      if ($(".showcase-slider").length) {
        var slider = $('.showcase-slider').slick({
          infinite: true,
          centerMode: true,
          arrows: false,
          centerPadding: '0px',
          slidesToShow: 6,
          responsive: [
            {
              breakpoint: 1199,
              settings: {
                slidesToShow: 5,
              },
            },
            {
              breakpoint: 992,
              settings: {
                slidesToShow: 4,
              },
            },
            {
              breakpoint: 768,
              settings: {
                slidesToShow: 3,
              },
            },
            {
              breakpoint: 580,
              settings: {
                slidesToShow: 2,
              },
            }
          ],
        });
        slider.on('wheel', (function (e) {
          e.preventDefault();

          if (e.originalEvent.deltaY < 0) {
            $(this).slick('slickPrev');
          } else {
            $(this).slick('slickNext');
          }
        }));
      }
    },
    temperature: function (e) {
      $('.celsius').on('click', function () {
        $('.celsius-block').css('display', 'block');
        $('.fahrenheit-block').css('display', 'none');
      })
      $('.fahrenheit').on('click', function () {
        $('.celsius-block').css('display', 'none');
        $('.fahrenheit-block').css('display', 'block');
      })
    },
    formValidation: function () {
      if ($(".contact-form").length) {
        $(".contact-form").validate();
      }
    },
    contactForm: function () {
      $(".contact-form").on("submit", function (e) {
        e.preventDefault();
        if ($(".contact-form").valid()) {
          var _self = $(this);
          _self
            .closest("div")
            .find('button[type="submit"]')
            .attr("disabled", "disabled");
          var data = $(this).serialize();
          $.ajax({
            url: "./assets/mail/contact.php",
            type: "post",
            dataType: "json",
            data: data,
            success: function (data) {
              $(".contact-form").trigger("reset");
              _self.find('button[type="submit"]').removeAttr("disabled");
              if (data.success) {
                document.getElementById("message").innerHTML =
                  "<h3 class='bg-success text-white p-3 mt-3'>Email Sent Successfully</h3>";
              } else {
                document.getElementById("message").innerHTML =
                  "<h3 class='bg-success text-white p-3 mt-3'>There is an error</h3>";
              }
              $("#message").show("slow");
              $("#message").slideDown("slow");
              setTimeout(function () {
                $("#message").slideUp("hide");
                $("#message").hide("slow");
              }, 3000);
            },
          });
        } else {
          return false;
        }
      });
    },
    videoPlay: function () {
      var $videoSrc;
      $('.play-btn').click(function () {
        $videoSrc = $(this).data("src");
        $("#video").attr('src', $videoSrc);
      });
      $('.btn-close').click(function () {
        $("#video").attr('src', ' ');
      });
    },
    map: function () {
      // window.initMap = initMap;
      var $mapholder = $('.map');
      if ($mapholder.length > 0) {
        const myLatlng = { lat: 51.509865, lng: -0.118092 };
        const map = new google.maps.Map(document.getElementById("google-map"), {
          zoom: 4,
          center: myLatlng,
        });
        // Create the initial InfoWindow.
        let infoWindow = new google.maps.InfoWindow({
          content: "Click the map to get weather info!",
          position: myLatlng,
        });

        infoWindow.open(map);
        // Configure the click listener.
        map.addListener("click", (mapsMouseEvent) => {
          // Close the current InfoWindow.
          infoWindow.close();
          // Create a new InfoWindow.
          infoWindow = new google.maps.InfoWindow({
            position: mapsMouseEvent.latLng,
          });
          infoWindow.setContent('30° | 02:PM - Rain Expected');
          infoWindow.open(map);
        });

      }
      var $mapholder1 = $('#google-map1');
      if ($mapholder1.length > 0) {
        var myLatens2 = { lat: 37.09024, lng: -95.712891 };
        var map1 = new google.maps.Map(document.getElementById("google-map1"), {
          zoom: 4,
          center: myLatens2,
        });
      }
      var $mapholder2 = $('#google-map2');
      if ($mapholder2.length > 0) {
        var myLatens3 = { lat: 55.378052, lng: -3.435973 };
        var map1 = new google.maps.Map(document.getElementById("google-map2"), {
          zoom: 4,
          center: myLatens3,
        });
      }
      var $mapholder3 = $('#google-map3');
      if ($mapholder3.length > 0) {
        var myLatens4 = { lat: 25.204849, lng: 55.270782 };
        var map1 = new google.maps.Map(document.getElementById("google-map3"), {
          zoom: 4,
          center: myLatens4,
        });
      }

    },

    chartJs: function () {
      var chart_block = $('.chart-block');
      if (chart_block.length > 0) {
        var ctx = document.getElementById("myChart");
        var myChart = new Chart(ctx, {
          type: 'pie',
          data: {
            labels: ["Current Time", "Sunset Time"],
            datasets: [{
              label: '# of Votes',
              data: [12.43, 6.40],
              backgroundColor: [
                '#FCA542',
                '#F0F5FF',
              ],
              borderColor: '#FCA542',
              borderWidth: 1
            }]
          },
          options: {
            rotation: 1 * Math.PI,
            circumference: 1 * Math.PI,
            legend: {
              display: false //This will do the task
            },
            responsive: true,
            plugins: {
              tooltip: {
                display: false // <-- this option disables tooltips
              }
            }

          }
        });
      }
    }
  }
  Init.i();
})(window, document, jQuery);

