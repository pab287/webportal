<?php
$activeModules = array();
$loggedSession = (object) $this->session->userdata("logged_in");
$activeModuleHasChildren = false;
$ctrModules = 0;
$ctrChildModules = 0;
if(isset($loggedSession->module_id) && $loggedSession->module_id){
    $roleId = $this->authenticate->getRoleId();
    $this->db->select("module_resource, role_resource");
    $this->db->where(array("role_id" => $roleId));
    $this->db->from("gccmaster.user_role_acl");
    $resource = $this->db->get();
    if($resource->num_rows() == 1){
        $row = $resource->row();
        $modules = @unserialize($row->module_resource);
        $roles = @unserialize($row->role_resource);
        $key = array_search($loggedSession->module_id, $modules);
        if($key !== false){ array_splice($modules, $key, 1); }

        $nextKey = array_search($loggedSession->module_id, $roles);
        if($nextKey !== false){ array_splice($roles, $nextKey, 1); }

        $roles = array_filter($roles, function($role) use ($loggedSession) {
            return strpos($role, $loggedSession->module_id . "-") !== 0;
        });

        $nRoles = array();
        if(is_array($roles) && !empty($roles)){
            foreach ($roles as $role) {
                $n = explode("-", $role);
                if(is_array($n) && !empty($n) && count($n) == 2){
                    $nRoles[$n[0]][] = $n[1];
                }
            }
        }

        if(is_array($modules) && !empty($modules)){
            $this->db->select("UPPER(TRIM(label)) label, UPPER(description) as description, icon, id");
            $this->db->where("is_active", 1);
            $this->db->where("status", 1);
            $this->db->where("parent_id", 0);
            $this->db->where("name !=", "core");
            $this->db->where_in("id", $modules);
            $this->db->order_by("sort", "asc");
            $mods = $this->db->get("gccmaster.modules");
            if($mods->num_rows() > 0){
                foreach ($mods->result_array() as $value) {
                    $value["has_pages"] = false;
                    $tempKey = $value["id"];
                    $tempPages = array();
                    if(isset($nRoles[$tempKey]) && is_array($nRoles[$tempKey]) && !empty($nRoles[$tempKey])){
                       $this->db->select("UPPER(TRIM(label)) label, url, id");
                        $this->db->where("is_active", 1);
                        $this->db->where("parent_id !=", 0);
                        $this->db->where_in("id", $nRoles[$tempKey]);
                        $this->db->order_by("TRIM(label)", "asc");
                        $this->db->order_by("sort", "asc");
                        $pages = $this->db->get("gccmaster.access_control_list");
                        $value["has_pages"] = $pages->num_rows() > 0;
                        if($pages->num_rows() > 0){
                            foreach ($pages->result_array() as $page) {
                                $tempPages[] = $page;
                            }
                        }
                        $value["pages"] = $tempPages;
                    }

                    $value["children"] = array();
                    $this->db->select("UPPER(TRIM(label)) label, UPPER(description) as description, icon, id");
                    $this->db->where("is_active", 1);
                    $this->db->where("status", 1);
                    $this->db->where("parent_id", $tempKey);
                    $this->db->order_by("sort", "asc");
                    $children = $this->db->get("gccmaster.modules");
                    $value["has_children"] = $children->num_rows() > 0;
                    $ctrChildModules = $children->num_rows();
                    if($children->num_rows() > 0){
                        $activeModuleHasChildren = true;
                        foreach ($children->result_array() as $child) {
                            $child["has_pages"] = false;
                            $childKey = $child["id"];
                            $childPages = array();
                            if(isset($nRoles[$childKey]) && is_array($nRoles[$childKey]) && !empty($nRoles[$childKey])){
                            $this->db->select("UPPER(TRIM(label)) label, url, id");
                                $this->db->where("is_active", 1);
                                $this->db->where_in("id", $nRoles[$childKey]);
                                $this->db->where("LOWER(label) !=", "back");
                                $this->db->order_by("sort", "asc");
                                $pages = $this->db->get("gccmaster.access_control_list");
                                $child["has_pages"] = $pages->num_rows() > 0;
                                if($pages->num_rows() > 0){
                                    foreach ($pages->result_array() as $page) {
                                        $childPages[] = $page;
                                    }
                                }
                                $child["pages"] = $childPages;
                            }
                            $value["children"][] = $child;
                        }
                    }
                    $activeModules[$value["id"]] = $value;
                }
            }
        }
    }

    $keys = array_keys($activeModules);
    $hideModules = is_array($keys) && !empty($keys) && count($keys) === 1 && intval($keys[0]) === 7;
}
?>

<?php if(is_array($activeModules) && !empty($activeModules)): ?>
<div id="m_header_menu" class="m-header-menu m-aside-header-menu-mobile m-aside-header-menu-mobile--offcanvas m-header-menu--skin-light m-header-menu--submenu-skin-light m-aside-header-menu-mobile--skin-dark m-aside-header-menu-mobile--submenu-skin-dark ">
    <ul class="m-menu__nav  m-menu__nav--submenu-arrow ">
        <?php if($hideModules === false): ?>
        <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel" data-menu-submenu-toggle="click"
            data-redirect="true">
            <a href="javascript:;" class="m-menu__link m-menu__toggle">
                <span class="m-menu__link-text">Modules</span>
                <i class="m-menu__hor-arrow la la-angle-down"></i>
                <i class="m-menu__ver-arrow la la-angle-right"></i>
            </a>
            <div class="m-menu__submenu  m-menu__submenu--classic m-menu__submenu--left">
                <span class="m-menu__arrow m-menu__arrow--adjust"></span>
                <ul class="m-menu__subnav">
                    <?php foreach ($activeModules as $id => $module): ?>
                    <?php if($module["has_children"] === false && $module["has_pages"] === true): ?>
                    <li class="m-menu__item m-menu__item--submenu">
                        <a href="javascript:;" class="m-menu__link m-menu__toggle">
                            <i class="m-menu__link-icon <?php echo $module["icon"] ? $module["icon"] : "la la-link"; ?>"></i>
                            <span class="m-menu__link-text"><?php echo $module["label"]; ?></span>
                            <i class="m-menu__hor-arrow la la-angle-right"></i>
                            <i class="m-menu__ver-arrow la la-angle-right"></i>
                        </a>
                        <?php if(is_array($module["pages"]) && !empty($module["pages"]) && count($module["pages"]) > 5): ?>
                        <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--right" style="width: 1000px;">
                            <span class="m-menu__arrow "></span>
                            <ul class="m-menu__subnav row">
                                <?php foreach ($module["pages"] as $page): ?>
                                <?php if(isset($page["url"]) && $page["url"]): ?>
                                <li class="m-menu__item col-4 col-md-4 col-lg-4 col-sm-12">
                                    <a  href="<?php echo site_url($page["url"]); ?>" class="m-menu__link">
                                        <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="m-menu__link-text"><?php echo $page["label"]; ?><br></span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php else: ?>
                        <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--right">
                            <span class="m-menu__arrow "></span>
                            <ul class="m-menu__subnav">
                                <?php foreach ($module["pages"] as $page): ?>
                                <?php if(isset($page["url"]) && $page["url"]): ?>
                                    <li class="m-menu__item">
                                        <a  href="<?php echo site_url($page["url"]); ?>" class="m-menu__link">
                                            <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                                <span></span>
                                            </i>
                                            <span class="m-menu__link-text"><?php echo $page["label"]; ?><br></span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </li>
                    <?php endif;?>
                    <?php endforeach;?>
                </ul>
            </div>
        </li>
        <?php endif; ?>
        
        <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel" data-menu-submenu-toggle="click"
            data-redirect="true">
            <a href="javascript:;" class="m-menu__link m-menu__toggle">
                <span class="m-menu__link-text">e-Forms</span>
                <i class="m-menu__hor-arrow la la-angle-down"></i>
                <i class="m-menu__ver-arrow la la-angle-right"></i>
            </a>
            <div class="m-menu__submenu  m-menu__submenu--classic m-menu__submenu--left" style="width: 375px;">
                <span class="m-menu__arrow m-menu__arrow--adjust"></span>
                <ul class="m-menu__subnav">
                    <?php if($activeModuleHasChildren === true): ?>
                        <?php foreach($activeModules as $id => $module): ?>
                            <?php if($module["has_children"] === true && is_array($module["children"]) && !empty($module["children"])): ?>
                                <?php foreach ($module["children"] as $child): ?>
                                <?php if($child["has_pages"] === true): ?>
                                <?php $tempIcon = 'la la-link';
                                if(isset($child["icon"]) && $child["icon"]) { $tempIcon = $child["icon"]; }
                                elseif (isset($module["icon"]) && $module["icon"]) { $tempIcon = $module["icon"]; }
                                ?>
                                <li class="m-menu__item m-menu__item--submenu">
                                    <a href="javascript:;" class="m-menu__link m-menu__toggle">
                                        <i class="m-menu__link-icon <?php echo $child["icon"] ? $child["icon"] : "la la-link"; ?>"></i>
                                        <span class="m-menu__link-text"><?php echo $child["label"]; ?></span>
                                        <i class="m-menu__hor-arrow la la-angle-right"></i>
                                        <i class="m-menu__ver-arrow la la-angle-right"></i>
                                    </a>
                                    <?php if(is_array($child["pages"]) && !empty($child["pages"]) && count($child["pages"]) > 5): ?>
                                    <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--right" style="width: 1000px;">
                                        <span class="m-menu__arrow "></span>
                                        <ul class="m-menu__subnav row">
                                            <?php foreach ($child["pages"] as $page): ?>
                                            <?php if(isset($page["url"]) && $page["url"]): ?>
                                            <li class="m-menu__item col-4 col-md-4 col-lg-4 col-sm-12">
                                                <a  href="<?php echo site_url($page["url"]); ?>" class="m-menu__link">
                                                    <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                                        <span></span>
                                                    </i>
                                                    <span class="m-menu__link-text"><?php echo $page["label"]; ?><br></span>
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <?php else: ?>
                                    <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--right">
                                        <span class="m-menu__arrow "></span>
                                        <ul class="m-menu__subnav">
                                            <?php foreach ($child["pages"] as $page): ?>
                                            <?php if(isset($page["url"]) && $page["url"]): ?>
                                                <li class="m-menu__item">
                                                    <a  href="<?php echo site_url($page["url"]); ?>" class="m-menu__link">
                                                        <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                                            <span></span>
                                                        </i>
                                                        <span class="m-menu__link-text"><?php echo $page["label"]; ?><br></span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <?php endif; ?>
                                </li>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </li>

    </ul>
</div>
<?php endif; ?>