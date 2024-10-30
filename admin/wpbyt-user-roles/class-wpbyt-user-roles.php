<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Bytes_Custom_Role_Manager {

    private $default_roles = array('administrator', 'editor', 'author', 'contributor', 'subscriber','shop_manager');

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu' ) );
    }

    public function add_menu() {
        add_menu_page(
            __('Role Manager', 'bytes-role-and-customer-based-pricing-for-woocommerce'),
            __('Role Manager', 'bytes-role-and-customer-based-pricing-for-woocommerce'),
            'manage_options',
            'custom-role-manager',
            array($this, 'menu_page_callback'),
            'dashicons-groups',
            6
        );
    }

    public function menu_page_callback() {
        global $wp_roles;

        if ( !isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }

        $editing_role = isset( $_GET[ 'edit_role' ] ) ? sanitize_key( $_GET['edit_role'] ) : false;

        $message = get_transient( 'bytes_role_operation_msg');
        if ( $message ) {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
            delete_transient('bytes_role_operation_msg');
        }

        if ( isset( $_POST[ 'add_edit_role' ] ) && current_user_can( 'manage_options' ) ) {

            if( ! isset( $_POST['custom_add_edit_role_nonce'] ) ||  ! wp_verify_nonce(  sanitize_text_field( wp_unslash( $_REQUEST['custom_add_edit_role_nonce'] ) ) , "custom_add_edit_role_action" ) ){
                echo '<div class="notice notice-error is-dismissible"><p>' . esc_attr__( "Nonce failed to validate. Please try again.", 'bytes-role-and-customer-based-pricing-for-woocommerce' ) . '</p></div>';
                die();
            }
           
            $this->handle_role_submission( $editing_role);
        }

        if ( isset( $_GET[ 'delete_role' ] ) && current_user_can( 'manage_options' ) ) {
            $this->delete_role();
        }

        $this->render_forms( $editing_role, $wp_roles );
    }

    private function handle_role_submission( $editing_role ) {
        if ( !check_admin_referer( 'custom_add_edit_role_action', 'custom_add_edit_role_nonce' ) ) {
            return;
        }

        $role_name = sanitize_text_field( $_POST['new_role_name'] );
        $display_name = sanitize_text_field( $_POST['new_role_display_name'] );
        $capabilities = isset( $_POST['capabilities'] ) ? array_map( 'sanitize_text_field', $_POST['capabilities'] ) : array();
        $new_capabilities = array_fill_keys( $capabilities, true );

        if ( $editing_role ) {
            remove_role( $editing_role );
            add_role( $role_name, $display_name, $new_capabilities );
            set_transient('bytes_role_operation_msg', __('Role edited successfully!', 'bytes-role-and-customer-based-pricing-for-woocommerce'), 60);
        } else {
            add_role( $role_name, $display_name, $new_capabilities );
            set_transient( 'bytes_role_operation_msg', __( 'Role saved successfully!', 'bytes-role-and-customer-based-pricing-for-woocommerce' ), 60);
        }

        wp_redirect( admin_url( 'admin.php?page=custom-role-manager' ) );
        exit;
    }

    private function delete_role() {
        if ( !check_admin_referer('custom_delete_role_action')) {
            return;
        }

        $role_to_delete = sanitize_key($_GET['delete_role']);

        if ( !in_array($role_to_delete, $this->default_roles) ) {
            remove_role($role_to_delete);
            set_transient('bytes_role_operation_msg', __('Role deleted successfully!', 'bytes-role-and-customer-based-pricing-for-woocommerce'), 60);
            wp_redirect(admin_url('admin.php?page=custom-role-manager'));
            exit;
        }
    }

    private function render_forms( $editing_role, $wp_roles ) {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Role Manager', 'bytes-role-and-customer-based-pricing-for-woocommerce') . '</h1>';

        // Add/Edit Role Form
        $this->render_edit_form($editing_role, $wp_roles);

        // Delete Role List
        $this->render_roles_list($wp_roles);

        echo '</div>';
    }

    private function render_edit_form( $editing_role, $wp_roles ) {
        echo '<form method="post" style="margin-top: 20px;">';
            wp_nonce_field('custom_add_edit_role_action', 'custom_add_edit_role_nonce');

            $current_capabilities = ($editing_role && isset($wp_roles->roles[$editing_role])) ? $wp_roles->roles[$editing_role]['capabilities'] : array(); ?>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="new_role_name">
                                <?php echo esc_html__('Role Name (ID):', 'bytes-role-and-customer-based-pricing-for-woocommerce');?>
                            </label>
                        </th>
                        <td>
                            <input type="text" name="new_role_name" required value="<?php echo ( $editing_role ? esc_attr( $editing_role ) : '' ); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="new_role_display_name">
                                <?php echo esc_html__('Display Name:', 'bytes-role-and-customer-based-pricing-for-woocommerce');?>
                            </label>
                        </th>
                        <td>
                            <input type="text" name="new_role_display_name" required value="<?php echo ($editing_role ? esc_attr($wp_roles->roles[$editing_role]['name']) : ''); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php echo esc_html__('Capabilities:', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label></th>
                        <td>
                            <?php
                            foreach ( bytes_brcbp_sanitize_array( $wp_roles->get_role( 'administrator' )->capabilities ) as $cap => $grant ) {
                                $is_checked = isset( $current_capabilities[ $cap ] ) && $current_capabilities[ $cap ] ? 'checked="checked"' : '';
                                ?>
                                 <label class="switch">
                                    <input type="checkbox" name="capabilities[]" value="<?php echo esc_attr( $cap ); ?>" <?php echo esc_attr( $is_checked ); ?>>
                                     <?php echo esc_html($cap); ?>
                                     <span class="slider"></span>
                                       
                                </label><br/>
                                <?php
                            } ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="submit">
                <input type="submit" name="add_edit_role" class="button button-primary" value="<?php echo esc_attr__('Save Role', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?>">
            </p>
        </form>
        <?php
    }

    private function render_roles_list( $wp_roles ) {  ?>
        <hr>
        <h2>
            <?php echo esc_html__( 'Custom Roles:', 'bytes-role-and-customer-based-pricing-for-woocommerce' ); ?>
        </h2>
        <ul>
            <?php
            foreach ( bytes_brcbp_sanitize_array( $wp_roles->roles ) as $role_name => $role_info ){
                if ( !in_array( $role_name, $this->default_roles ) ) {
                    $edit_link = wp_nonce_url( admin_url( 'admin.php?page=custom-role-manager&edit_role=' . $role_name ), 'custom_edit_role_action');
                    $delete_link = wp_nonce_url( admin_url( 'admin.php?page=custom-role-manager&delete_role=' . $role_name ), 'custom_delete_role_action' ); ?>
                    <li>
                        <span><?php echo esc_attr( $role_info['name'] );?> </span>
                        <a href="<?php echo esc_url( $edit_link ); ?>"><?php echo esc_html__('Edit', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?> </a> | 
                        <a href="<?php echo esc_url( $delete_link ); ?>">
                            <?php  echo esc_html__( 'Delete', 'bytes-role-and-customer-based-pricing-for-woocommerce' ); ?>
                        </a>
                    </li>
                <?php
                }
            } ?>
        </ul>
        <?php
    }
}

new Bytes_Custom_Role_Manager();