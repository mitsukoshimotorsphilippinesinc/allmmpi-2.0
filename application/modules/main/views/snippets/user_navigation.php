<div class="user-nav">
    <ul class="nav">	
        <?php
        // get all site links
        if (!isset($selected_menu)) $selected_menu = 'home';
        $navigation_links = $this->navigations_model->get_navigations("system_code = 'members'");
        //var_dump($navigation_links);
        if (!empty($navigation_links)) {
            foreach ($navigation_links as $l) {
                if ($l->is_active) {
                    $link = $this->config->item('base_url') . $l->url;

                    if ($selected_menu == $l->code)
                        echo "<li class='active'><a href='{$link}'>{$l->title}</a></li>";
                    else
                        echo "<li><a href='{$link}'>{$l->title}</a></li>";
                }
            }
        }
        ?>
    </ul>
</div>    