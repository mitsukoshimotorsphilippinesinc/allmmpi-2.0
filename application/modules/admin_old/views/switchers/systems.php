<?php

	$nav_tags = "";
	foreach ($this->systems as $item)
	{
		if ($item->is_visible == 1)
		{
			$active_class = $item->code == ci()->current_system ? 'active' : '';
			$nav_tags .= '<li ><a href="'.$item->url.'" class="'.$active_class.'">'.$item->pretty_name.'</a><li>';
		}
	}

?>

<div class="switcherMenu">
	<ul class="dropdown">
		<?= $nav_tags; ?>
	</ul>
</div>