<div id="sfWebDebugAssetsManager">
  <style>
  #sfWebDebugAssetsManager fieldset {
    border: none;
  }
  #sfWebDebugAssetsManager legend {
    font-weight:  bold;
    font-size:    1.2em;
    text-decoration: underline;
  }
  #sfWebDebugAssetsManager h4 {
    margin: 1px 0;
  }
  </style>
  <fieldset>
  <legend>Loaded packages</legend>
  <ul id="sfAssetsManager_loaded_packages">
  <?php foreach($loadedPackages as $package): ?>
    <li>
      <h4><?php echo $package->getName() ?></h4>
      Stylesheets :
      <ul>
        <?php foreach($package->getStylesheets() as $css): ?>
        <li><?php echo $css ?></li>
        <?php endforeach ?>
      </ul>
      Javascripts :
      <ul>
        <?php foreach($package->getJavascripts() as $js): ?>
        <li><?php echo $js ?></li>
        <?php endforeach ?>
      </ul>
    </li>
  <?php endforeach ?>
  </ul>
  </fieldset>
  
  <fieldset>
  <legend>Available packages</legend>
  <ul>
  <?php foreach($packages as $package): ?>
    <li>
      <h4><?php echo $package->getName() ?></h4>
      <div>
      Stylesheets: [<?php echo implode(', ', $package->getStylesheets()) ?>]<br />
      Javascripts: [<?php echo implode(', ', $package->getJavascripts()) ?>]
      </div>
    </li>
  <?php endforeach ?>
  </ul>
  </fieldset>
</div>