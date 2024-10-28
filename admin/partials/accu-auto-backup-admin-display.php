<?php
/**
 * Provide a administration area view for the plug-in
 *
 * PHP version 5
 *
 * This file is used to markup the administration-facing aspects of the plug-in.
 *
 * LICENSE: GPL-2.0+
 *
 * @category   Module
 * @package    Accu_Auto_Backup
 * @subpackage Accu_Auto_Backup/admin/partials
 * @author     Dhanashree Inc <business@dhanashree.com>
 * @copyright  2018 Dhanashree Inc
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GPL License
 * @version    SVN: 1.0.0
 * @link       http://www.dhanashree.com/
 * @since      File available since Release 1.0.0
 */
/**
 *  Common variable used in current file.
 */
$logo_file = 'accu-auto-backup-logo.png';
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<!-- Create a header in the default WordPress 'wrap' container -->

<div class="wrap">
	<div class="bootstrap-wrapper-disabled">
		<div class="accu-panel-heading">
			<img src="<?php echo ACCU_DIR_URL . $logo_file; ?>" />
			<h3><?php _e( 'Accu Auto Backup', 'accu-auto-backup' ); ?></h3>
		</div>
		<div class="accu_notice notice is-dismissible">
			<p></p>
		</div>
		<?php settings_errors(); ?>
		<?php
		if ( isset( $_GET['tab'] ) ) {
			$active_tab = $_GET['tab'];
		} else {
			//set display_options tab as a default tab.
			$active_tab = 'accu_general_settings';
		}
		?>
		<div class="accu-content">
			<h2 class="nav-tab-wrapper">
				<a href="?page=accu_auto_backup&tab=accu_general_settings" class="nav-tab 
				<?php echo 'accu_general_settings' == $active_tab ? 'nav-tab-active' : ''; ?>"><?php _e( 'General Settings', 'accu-auto-backup' ); ?></a>
				<a href="?page=accu_auto_backup&tab=accu_stored_backups" class="nav-tab <?php echo 'accu_stored_backups' == $active_tab ? 'nav-tab-active' : ''; ?>"><?php _e( 'Database Backups', 'accu-auto-backup' ); ?></a>
				<a href="?page=accu_auto_backup&tab=accu_terms" class="nav-tab <?php echo 'accu_terms' == $active_tab ? 'nav-tab-active' : ''; ?>"><?php _e( 'Terms & Conditions', 'accu-auto-backup' ); ?></a>
			</h2>
			<?php
			if ( 'accu_general_settings' == $active_tab ) {
				echo $this->accu_get_plugin_info();

				echo '<form method="post" action="options.php">';

				settings_fields( $this->_plugin_name );

				do_settings_sections( $this->_plugin_name );

				echo ' <input type="hidden" name="accu_action" value="add_foobar">';

				submit_button( __( 'Save Settings', 'accu-auto-backup' ) );


				echo '</form>';

				do_action( 'display_cron_info' );
			} elseif ( 'accu_stored_backups' == $active_tab ) {
				do_action( 'accu_backup_listing' );
			} elseif ( 'accu_terms' == $active_tab ) {
				$lione   = __( 'We as author/developer don’t give guarantee and don’t promise that the Accu Auto Backup plugin will work well on  all technical environment though we tried our best and taken care for its best availability and its workability.', 'accu-auto-backup' );
				$litwo   = __( 'The Accu Auto Backup plug-in from Dhanashree Inc provided \"as is\" without any warranties, obvious or implied.', 'accu-auto-backup' );
				$lithree = __( 'All taken backups is stored in zip format, you can download them from Database & File/Folder Backups.', 'accu-auto-backup' );
				$lifour  = __( 'We will not be responsible for any direct, indirect or any other damage or loss by usage of Accu Auto Backup plug-in.', 'accu-auto-backup' );
				$lifive  = __( 'User understands and confirm own responsibility up on agreement of usage of Accu Auto Backup plug-in.', 'accu-auto-backup' );
				echo '<ul class="accu_ul">
					<li><span>' . $lione . '</span></li>
					<li><span>' . $lithree . '</span></li>
					<li><span>' . $lifour . '</span></li>
					<li><span>' . $lifive . '</span></li>
				</ul>';
			}
			?>
		</div><!-- accu content -->
	</div><!-- /. bootstrap -->
</div><!-- /.wrap -->
