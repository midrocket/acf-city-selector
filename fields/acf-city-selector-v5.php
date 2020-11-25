<?php
    // exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( ! class_exists( 'acf_field_city_selector' ) ) :

        /**
         * Main class
         */
        class acf_field_city_selector extends acf_field {

            /*
             * Function index
             * - construct( $settings )
             * - render_field_settings( $field )
             * - render_field( $field )
             * - input_admin_head()
             * - load_value( $value, $post_id, $field )
             * - validate_value( $valid, $value, $field, $input )
             */

            /*
             *  __construct
             *
             *  This function will setup the class functionality
             *
             *  @param   n/a
             *  @return  n/a
             */
            function __construct( $settings ) {

                $this->name     = 'acf_city_selector';
                $this->label    = 'City Selector';
                $this->category = esc_attr__( 'Choice', 'acf-city-selector' );
                $this->defaults = array(
                    'show_labels'  => 1,
                    'which_fields' => 'all',
                    'use_select2'  => 0,
                );

                $this->settings = $settings;

                $select_country = apply_filters( 'acfcs_select_country_label', esc_attr__( 'Select a country', 'acf-city-selector' ) );
                $select_state   = apply_filters( 'acfcs_select_province_state_label', esc_attr__( 'Select a province/state', 'acf-city-selector' ) );
                $select_city    = apply_filters( 'acfcs_select_city_label', esc_attr__( 'Select a city', 'acf-city-selector' ) );

                $this->l10n = array(
                    'select_country' => $select_country,
                    'select_state'   => $select_state,
                    'select_city'    => $select_city,
                );

                // do not delete!
                parent::__construct();

            }

            /*
             * render_field_settings()
             *
             * Create extra settings for your field. These are visible when editing a field
             *
             * @type    action
             * @param   $field (array) the $field being edited
             * @return  n/a
             */
            function render_field_settings( $field ) {

                $label_options = array(
                    1 => esc_attr__( 'Yes', 'acf-city-selector' ),
                    0 => esc_attr__( 'No', 'acf-city-selector' )
                );
                acf_render_field_setting( $field, array(
                    'choices'      => $label_options,
                    'instructions' => esc_html__( 'Show field labels above the dropdown menus', 'acf-city-selector' ),
                    'label'        => esc_html__( 'Show labels', 'acf-city-selector' ),
                    'layout'       => 'horizontal',
                    'name'         => 'show_labels',
                    'type'         => 'radio',
                    'value'        => $field[ 'show_labels' ],
                ) );

                acf_render_field_setting( $field, array(
                    'choices'      => $label_options,
                    'instructions' => esc_html__( 'Use select2 for dropdowns', 'acf-city-selector' ),
                    'label'        => esc_html__( 'Select2', 'acf-city-selector' ),
                    'layout'       => 'horizontal',
                    'name'         => 'use_select2',
                    'type'         => 'radio',
                    'value'        => $field[ 'use_select2' ],
                ) );

                $countries = acfcs_get_countries( true, false, true );
                acf_render_field_setting( $field, array(
                    'choices'      => $countries,
                    'instructions' => esc_html__( 'Select a default country for a new field', 'acf-city-selector' ),
                    'label'        => esc_html__( 'Default country', 'acf-city-selector' ),
                    'name'         => 'default_country',
                    'type'         => 'select',
                ) );

                $default_country_fields = array(
                    'all'           => esc_attr__( 'All fields [default]', 'acf-city-selector' ),
                    'country_only'  => esc_attr__( 'Country only', 'acf-city-selector' ),
                    'country_state' => esc_attr__( 'Country + State/province', 'acf-city-selector' ),
                    'country_city'  => esc_attr__( 'Country + City', 'acf-city-selector' ),
                    'state_city'    => esc_attr__( 'State/province + City', 'acf-city-selector' ),
                );
                acf_render_field_setting( $field, array(
                    'choices'      => $default_country_fields,
                    'instructions' => esc_html__( 'Select which fields are used', 'acf-city-selector' ),
                    'label'        => esc_html__( 'Fields to use', 'acf-city-selector' ),
                    'name'         => 'which_fields',
                    'type'         => 'radio',
                ) );
            }

            /*
             * render_field()
             *
             * Create the HTML interface for your field
             *
             * @type    action
             * @param   $field (array) the $field being edited
             * @return  n/a
             */
            function render_field( $field ) {

                $default_country  = ( isset( $field[ 'default_country' ] ) && ! empty( $field[ 'default_country' ] ) ) ? $field[ 'default_country' ] : false;
                $prefill_cities   = [];
                $prefill_states   = [];
                $selected_country = ( isset( $field[ 'value' ][ 'countryCode' ] ) ) ? $field[ 'value' ][ 'countryCode' ] : false;
                $selected_state   = ( isset( $field[ 'value' ][ 'stateCode' ] ) ) ? $field[ 'value' ][ 'stateCode' ] : false;
                $selected_city    = ( isset( $field[ 'value' ][ 'cityName' ] ) ) ? $field[ 'value' ][ 'cityName' ] : false;
                $show_first       = true;
                $which_fields     = ( isset( $field[ 'which_fields' ] ) ) ? $field[ 'which_fields' ] : 'all';

                if ( false !== $default_country && false == $selected_country ) {
                    // New post with default country, so load all states + cities for $default_country
                    $prefill_states = acfcs_get_states( $default_country, $show_first, $field );
                    $prefill_cities = acfcs_get_cities( $default_country, false, $field );

                } elseif ( false !== $selected_country ) {
                    if ( in_array( $which_fields, [ 'all', 'country_state', 'state_city' ] ) ) {
                        $prefill_states = acfcs_get_states( $selected_country, $show_first, $field );
                    }
                    if ( in_array( $which_fields, [ 'all', 'country_city', 'state_city' ] ) ) {
                        $prefill_cities = acfcs_get_cities( $selected_country, $selected_state, $field );
                    }
                    if ( 'country_city' != $which_fields ) {
                        $selected_state = $selected_country . '-' . $selected_state;
                    }

                } elseif ( false == $default_country ) {
                    // no country set
                    if ( 'state_city' == $which_fields ) {
                        echo '<div class="acfcs"><div class="acfcs__notice field__message field__message--error">';
                        esc_html_e( "You haven't set a default country, so NO provinces/states and cities will be loaded.", 'acf-city-selector' );
                        echo '</div></div>';
                    }
                }

                // if repeater/flexible content and select2 set to yes
                if ( strpos( $field[ 'prefix' ], 'acfcloneindex' ) !== false && ( isset( $field[ 'use_select2' ] ) && 1 == $field[ 'use_select2' ] ) ) {
                    echo '<div class="acfcs"><div class="acfcs__notice field__message field__message--warning">';
                    if ( isset( $field[ 'parent_layout' ] ) ) {
                        // flexible content
                        esc_html_e( "Select2 doesn't work (yet) when adding a new layout in a flexible content block.", 'acf-city-selector' );
                    } else {
                        // repeater
                        esc_html_e( "Select2 doesn't work (yet) when a new row is added.", 'acf-city-selector' );
                    }
                    echo ' ';
                    if ( defined( 'IS_PROFILE_PAGE' ) ) {
                        esc_html_e( 'If you save your profile, select2 will work.', 'acf-city-selector' );
                    } else {
                        esc_html_e( 'Just save the post and select2 will work.', 'acf-city-selector' );
                    }
                    echo '</div></div>';
                }

                $prefill_values = [
                    'prefill_states' => $prefill_states,
                    'prefill_cities' => $prefill_cities,
                ];

                if ( 'state_city' != $which_fields ) {
                    echo acfcs_render_dropdown( 'country', $field, $selected_country, $prefill_values );
                }
                if ( 'all' == $which_fields || strpos( $which_fields, 'state' ) !== false ) {
                    echo acfcs_render_dropdown( 'state', $field, $selected_state, $prefill_values );
                }
                if ( 'all' == $which_fields || strpos( $which_fields, 'city' ) !== false ) {
                    echo acfcs_render_dropdown( 'city', $field, $selected_city, $prefill_values );
                }
            }


            /*
             * input_admin_head()
             *
             * This action is called in the admin_head action on the edit screen where your field is created.
             * Use this action to add CSS and JavaScript to assist your render_field() action.
             *
             * @type    action (admin_head)
             * @param   n/a
             * @return  n/a
             */
            function input_admin_head() {
            }


            /*
            *  input_admin_enqueue_scripts()
            *
            *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
            *  Use this action to add CSS + JavaScript to assist your render_field() action.
            *
            *  @type	action (admin_enqueue_scripts)
            *  @since	3.6
            *  @date	23/01/13
            *
            *  @param	n/a
            *  @return	n/a
            */
            function input_admin_enqueue_scripts() {

                $plugin_url     = $this->settings[ 'url' ];
                $plugin_version = $this->settings[ 'version' ];

                wp_register_script( 'acfcs-init', "{$plugin_url}assets/js/init.js", array( 'jquery', 'acf-input' ), $plugin_version );
                wp_enqueue_script( 'acfcs-init' );

                wp_register_script( 'acfcs-process', "{$plugin_url}assets/js/city-selector.js", array( 'jquery', 'acf-input' ), $plugin_version );
                wp_enqueue_script( 'acfcs-process' );

                // check field settings
                $all_info = acfcs_get_field_settings();

                if ( ! empty( $all_info ) && 1 == acfcs_check_array_depth( $all_info ) ) {
                    $load_vars[ 'default_country' ] = ( isset( $all_info[ 'default_country' ] ) ) ? $all_info[ 'default_country' ] : false;
                }
                $load_vars[ 'show_labels' ]  = ( isset( $all_info[ 'show_labels' ] ) ) ? $all_info[ 'show_labels' ] : true;
                $load_vars[ 'which_fields' ] = ( isset( $all_info[ 'which_fields' ] ) ) ? $all_info[ 'which_fields' ] : 'all';

                wp_localize_script( 'acfcs-process', 'city_selector_vars', $load_vars );

            }


            /*
            *  input_admin_footer()
            *
            *  This action is called in the admin_footer action on the edit screen where your field is created.
            *  Use this action to add CSS and JavaScript to assist your render_field() action.
            *
            *  @type	action (admin_footer)
            *  @since	3.6
            *  @date	23/01/13
            *
            *  @param	n/a
            *  @return	n/a
            */
            function input_admin_footer() {
            }


            /*
             * load_value()
             *
             * This filter is applied to the $value after it is loaded from the db
             * This returns false if no country/state is selected (but empty values are stored)
             *
             * @type    filter
             * @param   $value (mixed) the value found in the database
             * @param   $post_id (mixed) the $post_id from which the value was loaded
             * @param   $field (array) the field array holding all the field options
             * @return  $value
             */
            function load_value( $value, $post_id, $field ) {

                $state_code   = false;
                $country_code = ( isset( $value[ 'countryCode' ] ) ) ? $value[ 'countryCode' ] : false;

                if ( isset( $value[ 'stateCode' ] ) ) {
                    if ( 3 < strlen( $value[ 'stateCode' ] ) ) {
                        // $value[ 'stateCode' ] is longer than 3 characters, which starts with xx-
                        // where xx is the country code
                        $state_code = substr( $value[ 'stateCode' ], 3 );
                    } elseif ( 1 <= strlen( $value[ 'stateCode' ] ) ) {
                        // this is a fallback and is probably never reached
                        $state_code = $value[ 'stateCode' ];
                    }
                }

                if ( strlen( $country_code ) == 2 && false != $state_code ) {
                    global $wpdb;
                    $sql_query              = $wpdb->prepare( "SELECT country, state_name FROM {$wpdb->prefix}cities WHERE country_code= %s AND state_code= %s", $country_code, $state_code );
                    $row                    = $wpdb->get_row( $sql_query );
                    $value[ 'stateCode' ]   = $state_code;
                    $value[ 'stateName' ]   = ( isset( $row->state_name ) ) ? $row->state_name : false;
                    $value[ 'countryName' ] = ( isset( $row->country ) ) ? $row->country : false;
                }

                return $value;
            }


            /*
            *  update_value()
            *
            *  This filter is applied to the $value before it is saved in the db
            *
            *  @param	$value (mixed) the value found in the database
            *  @param	$post_id (mixed) the $post_id from which the value was loaded
            *  @param	$field (array) the field array holding all the field options
            *  @return	$value
            */
            function update_value( $value, $post_id, $field ) {

                $required = $field[ 'required' ];
                if ( 0 == $required ) {
                    if ( isset( $field[ 'which_fields' ] ) && 'all' == $field[ 'which_fields' ] || ! isset( $field[ 'which_fields' ] ) ) {
                        // if nothing is selected, set value to false
                        if ( empty( $value[ 'countryCode' ] ) && empty( $value[ 'stateCode' ] ) && empty( $value[ 'cityName' ] ) ) {
                            $value = false;
                        } elseif ( empty( $value[ 'stateCode' ] ) && empty( $value[ 'cityName' ] ) ) {
                            $value = false;
                        }
                    } elseif ( isset( $field[ 'which_fields' ] ) && 'country_only' == $field[ 'which_fields' ] ) {
                        if ( empty( $value[ 'countryCode' ] ) ) {
                            $value = false;
                        }
                    } elseif ( isset( $field[ 'which_fields' ] ) && 'country_state' == $field[ 'which_fields' ] ) {
                        if ( empty( $value[ 'countryCode' ] ) || empty( $value[ 'stateCode' ] ) ) {
                            $value = false;
                        }
                    } elseif ( isset( $field[ 'which_fields' ] ) && 'country_city' == $field[ 'which_fields' ] ) {
                        if ( empty( $value[ 'countryCode' ] ) || empty( $value[ 'cityName' ] ) ) {
                            $value = false;
                        }
                    } elseif ( isset( $field[ 'which_fields' ] ) && 'state_city' == $field[ 'which_fields' ] ) {
                        if ( empty( $value[ 'stateCode' ] ) || empty( $value[ 'cityName' ] ) ) {
                            $value = false;
                        }
                    }
                } else {
                    // field == required
                    if ( isset( $field[ 'which_fields' ] ) && 'all' == $field[ 'which_fields' ] || ! isset( $field[ 'which_fields' ] ) ) {
                        // if nothing is selected, set value to false
                        if ( empty( $value[ 'countryCode' ] ) && empty( $value[ 'stateCode' ] ) && empty( $value[ 'cityName' ] ) ) {
                            $value = false;
                        } elseif ( empty( $value[ 'countryCode' ] ) || empty( $value[ 'stateCode' ] ) || empty( $value[ 'cityName' ] ) ) {
                            $value = false;
                        }
                    } elseif ( isset( $field[ 'which_fields' ] ) && 'country_only' == $field[ 'which_fields' ] ) {
                        if ( empty( $value[ 'countryCode' ] ) ) {
                            $value = false;
                        }
                    } elseif ( isset( $field[ 'which_fields' ] ) && 'country_state' == $field[ 'which_fields' ] ) {
                        if ( empty( $value[ 'countryCode' ] ) || empty( $value[ 'stateCode' ] ) ) {
                            $value = false;
                        }
                    } elseif ( isset( $field[ 'which_fields' ] ) && 'country_city' == $field[ 'which_fields' ] ) {
                        if ( empty( $value[ 'countryCode' ] ) || empty( $value[ 'cityName' ] ) ) {
                            $value = false;
                        }
                    } elseif ( isset( $field[ 'which_fields' ] ) && 'state_city' == $field[ 'which_fields' ] ) {
                        if ( empty( $value[ 'stateCode' ] ) || empty( $value[ 'cityName' ] ) ) {
                            $value = false;
                        }
                    }
                }

                return $value;

            }


            /*
             * validate_value()
             *
             * This filter is used to perform validation on the value prior to saving.
             * All values are validated regardless of the field's required setting. This allows you to validate and return
             * messages to the user if the value is not correct
             *
             * @param   $valid (boolean) validation status based on the value and the field's required setting
             * @param   $value (mixed) the $_POST value
             * @param   $field (array) the field array holding all the field options
             * @param   $input (string) the corresponding input name for $_POST value
             * @return  $valid
             */
            function validate_value( $valid, $value, $field, $input ) {

                if ( 1 == $field[ 'required' ] ) {
                    $nothing       = esc_html__( "You didn't select anything.", 'acf-city-selector' );
                    $no_city       = esc_html__( "You didn't select a city.", 'acf-city-selector' );
                    $no_country    = esc_html__( "You didn't select a country.", 'acf-city-selector' );
                    $no_state      = esc_html__( "You didn't select a state.", 'acf-city-selector' );
                    $no_state_city = esc_html__( "You didn't select a state and city.", 'acf-city-selector' );

                    if ( 'all' == $field[ 'which_fields' ] ) {
                        if ( empty( $value[ 'countryCode' ] ) && empty( $value[ 'stateCode' ] ) && empty( $value[ 'cityName' ] ) ) {
                            $valid = $nothing;
                        } elseif ( empty( $value[ 'stateCode' ] ) && empty( $value[ 'cityName' ] ) ) {
                            $valid = $no_state_city;
                        } elseif ( empty( $value[ 'cityName' ] ) ) {
                            $valid = $no_city;
                        }
                    } elseif ( 'country_only' == $field[ 'which_fields' ] ) {
                        if ( empty( $value[ 'countryCode' ] ) ) {
                            $valid = $no_country;
                        }
                    } elseif ( 'country_state' == $field[ 'which_fields' ] ) {
                        if ( empty( $value[ 'countryCode' ] ) && empty( $value[ 'stateCode' ] ) ) {
                            $valid = $nothing;
                        } elseif ( empty( $value[ 'stateCode' ] ) ) {
                            $valid = $no_state;
                        }
                    } elseif ( 'country_city' == $field[ 'which_fields' ] ) {
                        if ( empty( $value[ 'countryCode' ] ) && empty( $value[ 'cityName' ] ) ) {
                            $valid = $nothing;
                        } elseif ( empty( $value[ 'cityName' ] ) ) {
                            $valid = $no_city;
                        }
                    } elseif ( 'state_city' == $field[ 'which_fields' ] ) {
                        if ( empty( $value[ 'stateCode' ] ) && empty( $value[ 'cityName' ] ) ) {
                            $valid = $nothing;
                        } elseif ( empty( $value[ 'stateCode' ] ) ) {
                            $valid = $no_state;
                        } elseif ( empty( $value[ 'cityName' ] ) ) {
                            $valid = $no_city;
                        }
                    }
                }

                return $valid;
            }
        }

        // initialize
        new acf_field_city_selector( $this->settings );

    endif; // class_exists check
