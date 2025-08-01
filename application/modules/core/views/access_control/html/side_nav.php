<?php
$currentPage = $this->core_layout->getBodyClass();
$hasBodyClass = $this->core_layout->hasBodyClass();
$navClass = isset($isChildNav) && $isChildNav? "m-menu__subnav": "m-menu__nav m-menu__nav--dropdown-submenu-arrow";
$currentMenu = array();
?>

<ul class="<?php echo $navClass; ?>">
<?php if(isset($aclMenu, $roleResource) && ($aclMenu && $roleResource)): ?>
	<?php foreach($aclMenu as $mKey => $menu){
		if(isset($isChildNav) && $isChildNav === true && in_array($menu["id"], $roleResource) && $menu["icon"] !== "m-menu__item--hidden" && $mKey >= 5){
			$currentMenu[] = $menu;
			if(isset($aclMenu[$mKey])){ unset($aclMenu[$mKey]); }
		}
	} ?>

	<!-- next nav here -->
	<?php if(isset($isChildNav) && $isChildNav === true && is_array($currentMenu) && !empty($currentMenu)): ?>
		<li class="m-menu__item m-menu__item--submenu m-menu__item--submenu--next-nav" data-menu-submenu-toggle="hover">
			<a href="#" class="m-menu__link m-menu__toggle">
				<i class="m-menu__link-bullet m-menu__link-bullet--dot">
					<span></span>
				</i>
				<span class="m-menu__link-text">More</span>
				<i class="m-menu__ver-arrow la la-angle-right"></i>
			</a>
			<div class="m-menu__submenu">
				<span class="m-menu__arrow"></span>
				<?php echo $this->load->view("core/access_control/html/side_nav", array("aclMenu" => $currentMenu, "roleResource" => $roleResource, "isChildNav" => true), true); ?>
			</div>
		</li>
	<?php endif; ?>
	<!-- next nav here -->

	<?php foreach($aclMenu as $mKey => $menu): ?>
	<?php
		$menuName = (isset($menu["name"]) && $menu["name"])? $menu["name"]: "";
		$activeMenu = "";
		
		$explodeCurrentPage = explode(" ", $currentPage);
		if(count($explodeCurrentPage) > 0){
			$activeMenu = ($hasBodyClass && in_array($menuName, $explodeCurrentPage))? " m-menu__item--active": "";
		}
		
		$subMenu = (isset($menu["children"]) && $menu["children"])? " m-menu__item--submenu":"";
		$toggleMenu = (isset($menu["children"]) && $menu["children"])? " data-menu-submenu-toggle='hover'":"";
		$menuToogle = (isset($menu["children"]) && $menu["children"])? " m-menu__toggle":"";
		$menuUrl = (isset($menu["url"]) && $menu["url"])? base_url($menu["url"]): "javascript:void(0);";
		$menuIcon = (isset($menu["icon"]) && $menu["icon"])? $menu["icon"]: "";
		$menuLabel = (isset($menu["label"]) && $menu["label"])? $menu["label"]: "";

		$identifier = (isset($menu->identifier) && $menu->identifier)? $menu->identifier: "";
		$isHidden = false;
		if($identifier){
			$explodeIdentifier = explode("--", $identifier);
			$explodeMenuIcon = explode("--", $menuIcon);
			$hideMenu = count($explodeMenuIcon) > 0 && in_array("hidden", $explodeMenuIcon)? true: false;
			$hideIdentifier = count($explodeIdentifier) > 0 && in_array("hidden", $explodeIdentifier)? true: false;
			$isHidden = $hideMenu || $hideIdentifier;
		}

		$nextMenu = array();
		if(isset($menu["children"]) && !empty($menu["children"])){
			foreach($menu["children"] as $key => $child){
				if(in_array($child["id"], $roleResource) && $child["icon"] !== "m-menu__item--hidden" && $key >= 5){
					$nextMenu[] = $child;
					if(isset($menu["children"][$key])){ unset($menu["children"][$key]); }
				}
			}
		}
	?>
	<?php if(isset($menu["id"]) && ($menu["id"] && in_array(intval($menu["id"]), $roleResource)) && $isHidden === false): ?>
		<li class="m-menu__item<?php echo "{$subMenu}{$activeMenu}"; ?>" <?php echo $toggleMenu; ?>>
			<a  href="<?php echo $menuUrl; ?>" class="m-menu__link<?php echo $menuToogle; ?>">
				<span class="m-menu__item-here"></span>
				<?php if(!isset($isChildNav)): ?>
				<i class="m-menu__link-icon <?php echo $menuIcon; ?>"></i>
				<?php endif; ?>
				<span class="m-menu__link-text"><?php echo $menuLabel; ?></span>
			<?php if(isset($menu["children"]) && $menu["children"]): ?>
				<i class="m-menu__ver-arrow la la-angle-right"></i>
			<?php endif; ?>
			</a>
			<?php if(isset($menu["children"]) && $menu["children"]): ?>
			<div class="m-menu__submenu">
				<span class="m-menu__arrow"></span>
				<ul class="m-menu__subnav">
				<!-- next nav here -->
				<?php if(is_array($nextMenu) && !empty($nextMenu)): ?>
					<li class="m-menu__item m-menu__item--submenu m-menu__item--submenu--next-nav" data-menu-submenu-toggle="hover">
						<a href="#" class="m-menu__link m-menu__toggle">
							<i class="m-menu__link-bullet m-menu__link-bullet--dot">
								<span></span>
							</i>
							<span class="m-menu__link-text">More</span>
							<i class="m-menu__ver-arrow la la-angle-right"></i>
						</a>
						<div class="m-menu__submenu">
							<span class="m-menu__arrow"></span>
							<?php echo $this->load->view("core/access_control/html/side_nav", array("aclMenu" => $nextMenu, "roleResource" => $roleResource, "isChildNav" => true), true); ?>
						</div>
					</li>
				<?php endif; ?>
				<!-- next nav here -->

				<?php foreach($menu["children"] as $child):
					$menuIconChild = (isset($child["icon"]) && $child["icon"])? $child["icon"]: "";
					if (in_array($child["id"], $roleResource) && $menuIconChild !== "m-menu__item--hidden"){
					$childUrl = (isset($child["url"]) && $child["url"])? base_url($child["url"]): "javascript:void(0);";
					$childLabel = (isset($child["label"]) && $child["label"])? $child["label"]: ""; ?>
						<li class="m-menu__item  m-menu__item--parent">
							<a  href="<?php echo $childUrl; ?>" class="m-menu__link ">
								<span class="m-menu__item-here"></span>
								<span class="m-menu__link-text"><?php echo $childLabel; ?></span>
							</a>
						</li>
				<?php }
				endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>
		</li>
	<?php endif; ?>
	<?php endforeach; ?>
	<?php if(!isset($isChildNav)): ?>
	<li class="m-menu__item">
		<a  href="<?php echo base_url("portal/index"); ?>" class="m-menu__link ">
			<span class="m-menu__item-here"></span>
			<i class="m-menu__link-icon fa fa-globe"></i>
			<span class="m-menu__link-text">Portal</span>
		</a>
	</li>
	<?php endif; ?>
<?php else: ?>
	<li class="m-menu__item">
		<a  href="<?php echo base_url("portal/index"); ?>" class="m-menu__link ">
			<span class="m-menu__item-here"></span>
			<i class="m-menu__link-icon fa fa-globe"></i>
			<span class="m-menu__link-text">Portal</span>
		</a>
	</li>
<?php endif; ?>
</ul>
