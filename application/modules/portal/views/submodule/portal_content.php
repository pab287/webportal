<?php 
if(isset($modules) && $modules):
    $hasModules = array(); $lockedModules = array();
    foreach($modules as $module):
        if(in_array($module->id, $module_resource)):
            $hasModules[] = $module;
        else:
            $lockedModules[] = $module;
        endif;
    endforeach;
?>
<div class="row row-navigation_icon">
    <?php if(isset($hasModules) && count($hasModules) > 0): ?>
        <?php foreach ($hasModules as $key => $module): ?>
        <div class="col-xl-3">
            <a href="javascript:void(0);" class="module_redirect" data-id="<?php echo $module->id; ?>">
            <div class="m-portlet m-portlet--fit m-portlet--skin-dark <?php echo ($module->bg_color)? strtolower($module->bg_color): strtolower("m--bg-secondary"); ?>">
                <div class="m-portlet__body">
                <h3><?php echo ($module->label)? strtoupper($module->label): strtoupper("No module name"); ?></h3>
                <p><?php echo ($module->description)? strtoupper($module->description): "&nbsp;"; ?></p>
                </div>
            </div>
            </a>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if(isset($lockedModules) && count($lockedModules) > 0): ?>
        <?php foreach ($lockedModules as $key => $module): ?>
        <div class="col-xl-3">
            <div class="m-portlet m-portlet--fit m-portlet--skin-dark m--bg-disabled">
                <div class="m-portlet__body">
                <h3><?php echo ($module->label)? strtoupper($module->label): strtoupper("No module name"); ?></h3>
                <p><?php echo ($module->description)? strtoupper($module->description): "&nbsp;"; ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php else: ?>
    <div class="col-xl-3">
        <div class="m-portlet m-portlet--fit m-portlet--skin-dark m--bg-warning">
            <div class="m-portlet__body">
                <h3>No Data</h3>
                <p>No modules found</p>
            </div>
        </div>
    </div>
<?php endif; ?>
</div>