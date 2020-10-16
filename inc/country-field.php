<?php

    /*
     * Set admin-ajax.php on the front side (by default it is available only for Backend)
     */
    function city_selector_ajaxurl() {
        ?>
        <script type="text/javascript">
            var ajaxurl = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
        </script>
        <?php
    }
    add_action( 'wp_head', 'city_selector_ajaxurl' );
    add_action( 'login_head', 'city_selector_ajaxurl' );

    /*
     * Get states by country code
     *
     * @param bool $country_code
     * @return JSON Object
     */
    function get_states_call() {

        if ( isset( $_POST[ 'country_code' ] ) ) {
            $country_code     = $_POST[ 'country_code' ];
            $transient_states = acfcs_get_states( $country_code );

            $i          = 1;
            $items      = [];
            $items[ 0 ] = [
                'country_code'  => '',
                'country_state' => '',
                'state_code'    => '',
                'state_name'    => esc_html__( 'Select a province/state', 'acf-city-selector' ),
            ];

            foreach ( $transient_states as $key => $label ) {
                $items[ $i ][ 'country_code' ] = $country_code;
                $items[ $i ][ 'state_code' ]   = $key;
                if ( $label != 'N/A' ) {
                    $items[ $i ][ 'state_name' ]    = $label;
                    $items[ $i ][ 'country_state' ] = $key;
                } else {
                    $items[ $i ][ 'state_name' ]    = $country_code;
                    $items[ $i ][ 'country_state' ] = $key;
                }
                $i++;
            }
            echo json_encode( $items );
            wp_die();
        }
    }
    add_action( 'wp_ajax_get_states_call', 'get_states_call' );
    add_action( 'wp_ajax_nopriv_get_states_call', 'get_states_call' );

    /*
     * Get cities by state code and/or country code
     *
     * @return JSON Object
     */
    function get_cities_call() {

        if ( isset( $_POST[ 'state_code' ] ) ) {
            if ( 6 <= strlen( $_POST[ 'state_code' ] ) ) {
                $codes        = explode( '-', $_POST[ 'state_code' ] );
                $country_code = $codes[ 0 ];
                $state_code   = $codes[ 1 ];
            } elseif ( strpos( $_POST[ 'state_code' ], 'FR-' ) !== false ) {
                $country_code = substr( $_POST[ 'state_code' ], 0, 2 );
                $state_code   = substr( $_POST[ 'state_code' ], 3 );
            } elseif ( 2 == strlen( $_POST[ 'state_code' ] ) ) {
                // if 2 == strlen( $_POST[ 'state_code' ] ) then it's a country code
                $country_code = $_POST[ 'state_code' ];
                $state_code   = false;
            } else {
                $codes        = explode( '-', $_POST[ 'state_code' ] );
                $country_code = $codes[ 0 ];
                $state_code   = $codes[ 1 ];
            }

            $cities_transient = acfcs_get_cities( $country_code, $state_code );

            // shown after state change
            $first_item = [
                'id'        => '',
                'city_name' => esc_html__( 'Select a city', 'acf-city-selector' ),
            ];
            $items  = array();
            if ( ! empty( $cities_transient ) ) {
                foreach ( $cities_transient as $city ) {
                    $items[] = [
                        'id'        => $city,
                        'city_name' => $city,
                    ];
                }
                uasort( $items, 'acfcs_sort_array_with_quotes' );
                array_unshift( $items, $first_item );
                echo json_encode( $items );
                wp_die();
            }
        }
    }
    add_action( 'wp_ajax_get_cities_call', 'get_cities_call' );
    add_action( 'wp_ajax_nopriv_get_cities_call', 'get_cities_call' );
