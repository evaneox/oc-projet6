$(function(){

    $(window).load(function(){
        /*************************************************
         * Init script
         ***********************************************************/
        var $langSwitcher   = $('#lang');
        var geocoder        = new google.maps.Geocoder;
        var geolocBtn       = $('.geolocalisation');
        var input           = /** @type {!HTMLInputElement} */( document.getElementsByClassName('geo_autocomplete') );
        var $container      = $('#pictures-upload');
        var index           = $container.data('index');
        var importantAction = $('.pop_action');

        /**
         * Customize select elements
         */
        $('select.style_full').multipleSelect({
            'keepOpen'          :true,
            'isOpen'            : true,
            'width'             : '100%',
            'selectAll'         : false,
            'filter'            : true,
            'noMatchesFound'    : _lang_no_result
        });

        $('select.style_min').multipleSelect({
            'keepOpen'          :true,
            'isOpen'            : true,
            'width'             : '100%',
            'selectAll'         : false,
            'noMatchesFound'    : _lang_no_result
        });

        /**
         * Customize slider elements
         */
        $(".porto_galla").owlCarousel({
            /*direction: site_dir,*/
            slideSpeed : 1200,
            autoPlay : 6000,
            autoHeight : false,
            items:1,
            itemsDesktop: false,
            itemsDesktopSmall: false,
            itemsTablet: false,
            itemsTabletSmall: false,
            itemsMobile: false,
            stopOnHover : true,
            navigation : true,
            pagination : true,
            navigationText : [
                "<span class='enar_owl_p'><i class='ico-angle-left'></i></span>",
                "<span class='enar_owl_n'><i class='ico-angle-right'></i></span>"]
        });

        /**
         * Loading google maps autocompletion for each input address
         */
        if(input != null){
            for (var i = 0; i < input.length; i++) {
                var autocomplete = new google.maps.places.Autocomplete(input[i]);
            }
        }

        /**
         * Change checkbox style
         */
        $('input[type=checkbox]').iCheck({checkboxClass: 'icheckbox_minimal'})


        /*************************************************
         * Common functions
         ***********************************************************/

        /**
         * This function use HTML5 geolocation and display a address
         */
        function getCurrentPosition(){
            if (navigator.geolocation){
                navigator.geolocation.getCurrentPosition(function(position){
                    var latlng = {lat: parseFloat(position.coords.latitude), lng: parseFloat(position.coords.longitude)};
                    geocoder.geocode({'location': latlng}, function(results, status) {
                        if (status === 'OK') {
                            if (results[0]) {
                                $('.geo_autocomplete').val(results[0].formatted_address);
                            }
                        }else { console.log('Geocoder  failed due to: ' + status); }
                    });
                });
            } else{
                swal({
                    title: 'Oops !',
                    text: "Votre navigateur ne prend pas en charge la géolocalisation.",
                    confirmButtonColor: '#525564'
                });
            }
        }

        /**
         * When user use the geolocation button, we try to determine her address
         */
        geolocBtn.on('click', function(event){
            event.preventDefault();
            getCurrentPosition();
        });

        /**
         * Checkbox transformation, during a change of status the list is updated
         */
        $('#sport_filter .ms-parent input[type=checkbox]').iCheck({checkboxClass: 'icheckbox_minimal'})
            .on('ifToggled', function(event) {
                var value = $('select.style_full').multipleSelect('getSelects');
                $('select.style_full').multipleSelect('setSelects', value);
                getResultsWithFilter();
            });

        $('.full_contact_form .ms-parent input[type=checkbox]').iCheck({checkboxClass: 'icheckbox_minimal'})
            .on('ifToggled', function(event) {
                var value  = $(this).closest('input').attr('value');
                var elem   = $(this).parent().parent().parent().parent().parent().parent().parent().parent().find('select option[value='+value+']');
                if(elem.is(':selected')){
                    elem.removeAttr('selected');
                }else{
                    elem.attr('selected', 'selected');
                }
            });

        /**
         * Management for important action, will be stop the process and show confirmation dialog
         */
        importantAction.on('click', function(event){
            event.preventDefault();
            var redirect = $(this).attr('href');
            swal({
                title: _lang_confirm,
                text: _lang_confirm_explain,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: _lang_confirm_yes,
                cancelButtonText: _lang_confirm_no
            }).then(function () {
                $(location).attr('href', redirect)
            })
        });

        /*************************************************
         * Language switcher
         ***********************************************************/
        if(_locale == ""){
            _locale = "fr";
        }

        $langSwitcher.on('change', function() {
            $(location).attr('href', this.value);
        });

        /*************************************************
         * Listings page functions
         ***********************************************************/

        /********
         * Control distance filter
         **********/
        if ( $.isFunction($.fn.noUiSlider) ) {
            $('#shop_price_slider').noUiSlider({
                start: [ 0, 60 ],
                step: 5,
                snap: false,
                connect: true,
                animate: true,
                range: {
                    'min': 0,
                    'max': 60
                },
                format: wNumb({
                    decimals: 0
                })
            });
            $('#shop_price_slider').Link('lower').to($('#shop_price_slider_lower'), null, wNumb({}));
            $('#shop_price_slider').Link('lower').to($('#min_distance'), null, wNumb({}));
            $('#shop_price_slider').Link('upper').to($('#shop_price_slider_upper'), null, wNumb({}));
            $('#shop_price_slider').Link('upper').to($('#max_distance'), null, wNumb({}));

            /**
             * If distance filter change, we should update the results
             */
            $('#shop_price_slider').on('change', function(){
                getResultsWithFilter();
            });

            /**
             * When user want to delete distance filter
             */
            $('#filter_distance_delete').on('click', function(event){
                event.preventDefault();
                $('#shop_price_slider').val([0,60]);
                getResultsWithFilter();
            });
        }

        /**
         * When user want to delete sports filter
         */
        $('#filter_sports_delete').on('click', function(event){
            event.preventDefault();
            $("select").multipleSelect("uncheckAll");

            $('.icheckbox_minimal').each(function(){
                $(this).removeClass('checked');
            });
            getResultsWithFilter();
        });

        /**
         * When user need to filter by distance or type,
         * or cancel it we should update the result
         */
        function getResultsWithFilter(){

            // get the filter values and convert to array
            var distance_filter = $('#shop_price_slider').val().toString().split(',');
            var sports_filter   = $('select').multipleSelect('getSelects');
            var min_distance    = distance_filter[0];
            var max_distance    = distance_filter[1];

            //console.log(sports_filter);

            // For each spot element
            $('.products_filter li').each(function(){

                var distance = $(this).data('distance');
                var sports   = $(this).data('sports').toString().split(',');

                //console.log(sports);

                // We show only this spot if the distance of this spot is between the limit
                if( distance >= min_distance && distance <= max_distance){

                    // And we filter again the result if user want to see only some sports
                    if(sports_filter.length > 0){
                        var spot_validate = false;
                        $.each( sports , function(index, value){
                            if(sports_filter.indexOf(value) !== -1){
                                spot_validate = true;
                            }
                        });
                        if(spot_validate){
                            $(this).show();
                        }else{
                            $(this).hide();
                        }
                    }else{
                        $(this).show();
                    }
                }else{
                    $(this).hide();
                }
            });
        }

        /*************************************************
         * Edit page functions
         ***********************************************************/

        /**
         * tinyMCE configuration
         */
        $('.tinymce').summernote({
            minHeight: 350,
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['style']],
                ['misc', ['undo', 'redo']],
                ['style', ['bold', 'italic', 'underline', 'clear', 'strikethrough']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['hr']],
            ],
            styleTags: ['p', 'blockquote', 'h3', 'h4']
        });


        /*********
         * Management pictures uploading
         **************/
        $('.upload-row ul li').each(function(){
            $(this).addClass('error');
        });

        if($container.find(':input').length == 0 && $container.length) {
            for(i=0; i < 3; i++){
                addFieldImage();
            }
        }

        /**
         * Adding a new file upload field
         *
         * @return void
         */
        function addFieldImage() {
            var template = $container.attr('data-prototype')
                .replace(/__name__/g,        index);
            var $prototype = $(template);
            $container.append($prototype);
            index++;
        }
    });

});

