<?php
class MiraiVdpImporter
{
  /**
  * Holds the values to be used in the fields callbacks
  */
  private $options;

  /**
  * Start up
  */
  public function __construct()
  {
    add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
  }

  /**
  * Add options page
  */
  public function add_plugin_page()
  {
    // This page will be under "Settings"
    add_options_page(
      'Mirai VDP Importer',
      'VDP Importer',
      'manage_options',
      'mirai-vdp-importer',
      array( $this, 'create_admin_page' )
    );
  }

  /**
  * Options page callback
  */
  public function create_admin_page()
  {
    // Set class property
    $this->options = get_option( 'elementor_sportrick_options' );
    ?>
    <div class="wrap">
      <h1>Mirai VDP Importer</h1>
      <br><br>
      <div id="import-cmds">
        <button id="import-attr-btn" class="button button-primary">Importa attributi da cat e tag</button>
        <br><br>
        <button id="import-tags-to-sellers-btn" class="button button-primary">Assegna i vendor tramite tag</button>
        <br><br>
        <button id="assign-sellers-btn" class="button button-primary">Assegna i vendor tramite csv</button>
        <br><br>
      </div>
      <div id="import-log"></div>
    </div>
    <?php
  }

}

if( is_admin() ){
  $mirai_vdp_importer_page = new MiraiVdpImporter();
}
