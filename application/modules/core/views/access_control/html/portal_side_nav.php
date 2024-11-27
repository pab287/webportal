<?php
$currentPage = $this->core_layout->getBodyClass();
$hasBodyClass = $this->core_layout->hasBodyClass();
?>
<ul class="m-menu__nav  m-menu__nav--dropdown-submenu-arrow ">
<?php if(isset($aclMenu, $roleResource) && ($aclMenu && $roleResource)): ?>
<?php foreach($aclMenu['modules'] as $menu): ?>
<?php
	$menuName = (isset($menu->name) && $menu->name)? $menu->name: "";
	$activeMenu = "";
	
	$explodeCurrentPage = explode(" ", $currentPage);
	if(count($explodeCurrentPage) > 0){
		$activeMenu = ($hasBodyClass && in_array($menuName, $explodeCurrentPage))? " m-menu__item--active": "";
	}
	
	$subMenu = (isset($menu->children) && $menu->children)? " m-menu__item--submenu":"";
	$toggleMenu = (isset($menu->children) && $menu->children)? " data-menu-submenu-toggle='hover'":"";
	$menuToogle = (isset($menu->children) && $menu->children)? " m-menu__toggle":"";
	$menuUrl = (isset($menu->url) && $menu->url)? base_url($menu->url): "javascript:void(0);";
	$menuIcon = (isset($menu->icon) && $menu->icon)? $menu->icon: "";
	$menuLabel = (isset($menu->label) && $menu->label)? $menu->label: "";
?>
<?php if(isset($menu->id) && ($menu->id && in_array(intval($menu->id), (array) $roleResource)) && $menuIcon !== "m-menu__item--hidden"): ?>
	<li class="m-menu__item<?php echo "{$subMenu}{$activeMenu}"; ?>" aria-haspopup="true"<?php echo $toggleMenu; ?> data-skin="dark" data-toggle="m-tooltip" data-placement="left" data-original-title="<?=strtoupper($menu->description) ?>">
		<a  href="<?php echo $menuUrl; ?>" class="m-menu__link<?php echo $menuToogle; ?>">
			<span class="m-menu__item-here"></span>
			<i class="m-menu__link-icon <?php echo $menuIcon; ?>"></i>
			<span class="m-menu__link-text"><?php echo $menuLabel; ?></span>
		<?php if(isset($menu->children) && $menu->children): ?>
			<i class="m-menu__ver-arrow la la-angle-right"></i>
		<?php endif; ?>

		</a>
	<?php if($menu->parent_id != 0): ?>
		<div class="m-menu__submenu">
			<span class="m-menu__arrow"></span>
			<ul class="m-menu__subnav">
		<?php foreach($menu->children as $child):
			$menuIconChild = (isset($child["icon"]) && $child["icon"])? $child["icon"]: "";
			if(in_array($child["id"], $roleResource) && $menuIconChild !== "m-menu__item--hidden"):
			$childUrl = (isset($child["url"]) && $child["url"])? base_url($child["url"]): "javascript:void(0);";
			$childLabel = (isset($child["label"]) && $child["label"])? $child["label"]: ""; ?>
				<li class="m-menu__item  m-menu__item--parent" aria-haspopup="true" >
					<a  href="<?php echo $childUrl; ?>" class="m-menu__link ">
						<span class="m-menu__item-here"></span>
						<span class="m-menu__link-text"><?php echo $childLabel; ?></span>
					</a>
				</li>
		<?php endif; endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	</li>
<?php endif; ?>
<?php endforeach; ?>
<li class="m-menu__item" aria-haspopup="true" >
	<a  href="<?php echo base_url("portal/index"); ?>" class="m-menu__link ">
		<span class="m-menu__item-here"></span>
		<i class="m-menu__link-icon fa fa-globe"></i>
		<span class="m-menu__link-text">Portal</span>
	</a>
</li>
<?php else: ?>
<li class="m-menu__item" aria-haspopup="true" >
	<a  href="<?php echo base_url("portal/index"); ?>" class="m-menu__link ">
		<span class="m-menu__item-here"></span>
		<i class="m-menu__link-icon fa fa-globe"></i>
		<span class="m-menu__link-text">Portal</span>
	</a>
</li>
<?php endif; ?>
</ul>