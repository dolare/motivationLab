(function($){

    var offset;

    var methods = {

        /**
         * Main fn method
         */
        init : function( options ) {
            var defaults = {
                verticalOffset: 100,
                overlay: 0.5,
                closeButton: null
            }
            
            var overlay = $("<div id='lean_overlay'></div>");
            
            $( "body" ).append(overlay);
                 
            options = $.extend( defaults, options );

            this.each( function() {
            
                var o = options,
                    window_height = $(window).height(); 
               
                offset = parseInt( o.verticalOffset );

                $(this).on( 'click', function(e) {

                    var modal_id = methods.get_target( $(this) ), 
                        $modal = $( modal_id ), 
                        resize = function() {
                            methods.resize( $modal );
                        },
                        modal_height, 
                        modal_width;                    

                    $( "#lean_overlay" ).on( 'click', function() { 
                        methods.close( $modal );                    
                    });
                    
                    $( o.closeButton ).on( 'click', function() { 
                        methods.close( $modal );                    
                    });
                                
                    $modal.outerHeight();
                    $modal.outerWidth();

                    $('#lean_overlay').css({ 'display' : 'block', opacity : 0 });

                    $('#lean_overlay').fadeTo(200,o.overlay);

                    $modal.css({                     
                        'display' : 'block',
                        'position' : 'fixed',
                        'opacity' : 0,
                        'z-index': 100000,                    
                    });

                    resize();
                    $modal.resize( resize );
                    $(window).resize( resize );

                    $modal.fadeTo(200,1);

                    /* Provide an event hook for other scripts to use. */
                    $( 'body' ).trigger( 'charitable:modal:open' );

                    e.preventDefault();
                        
                });
             
            });
        }, 

        /**
         * Return the ID of the modal to open.
         */
        get_target : function( $trigger ) {
            var id = '#' + $trigger.data( 'trigger-modal' );

            if ( 1 === id.length ) {
                id = $trigger.attr( "href" );
            }

            return id;
        },

        /**
         * Close modal 
         */
        close : function( $modal ) {
            $( "#lean_overlay" ).fadeOut(200);
            $modal.css({ 'display' : 'none' });
            methods.reset( $modal );

            /* Provide an event hook for other scripts to use. */
            $( 'body' ).trigger( 'charitable:modal:close' );
        }, 

        /**
         * Reset modal CSS
         */
        reset : function( $modal ) {            
            $modal.css({
                'bottom' : 'auto', 
                'overflowY' : 'auto'
            });
        },

        /**
         * Resize modal
         */
        resize : function( $modal ) {            
            var window_height = $(window).height(),            
                modal_width = $modal.outerWidth(),
                modal_height = $modal.outerHeight()
                available_offset = window_height - modal_height, 
                modal_is_too_tall = ( function() {
                    var modal_calc_height = modal_height + ( 2 * offset );
                    return window_height < modal_calc_height;
                })(), 
                modal_css = {
                    'left' : 50 + '%',
                    'margin-left' : -( modal_width / 2 ) + "px",
                    'top' : offset + "px"
                };

            if ( modal_is_too_tall ) {
                if ( available_offset > 0 ) {
                    var v_offset = available_offset / 2;

                    modal_css.top = v_offset + 'px';
                    modal_css.bottom = v_offset + 'px';
                }

                if ( available_offset <= 0 ) {
                    modal_css.top = '10px';
                    modal_css.bottom = '10px';
                    modal_css.overflowY = 'scroll';                
                }
            }                   

            $modal.css( modal_css );

            /* Provide an event hook for other scripts to use. */
            $( 'body' ).trigger( 'charitable:modal:resize' );
        }        
    };   
 
    /** 
     * Register this as a jQuery function.
     */
    $.fn.extend({ 
        leanModal : function( method_or_options ) {
            if ( methods[ method_or_options ] ) {
                return methods[  method_or_options  ].apply( this, Array.prototype.slice.call( arguments, 1 ));
            } else if ( typeof  method_or_options  === 'object' || ! method_or_options  ) {
                // Default to "init"
                return methods.init.apply( this, arguments );
            } else {
                $.error( 'Method ' +   method_or_options  + ' does not exist on jQuery.leanModal' );
            }    
        }
    });

    /**
     * Init function.
     */
    $( document ).ready( function() {
        $( '[data-trigger-modal]' ).leanModal({
            closeButton : ".modal-close"
        });
    }); 
     
})(jQuery);