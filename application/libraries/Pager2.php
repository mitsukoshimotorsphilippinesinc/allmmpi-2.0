<?php if (!defined('BASEPATH')) exit('No direct script access allowed.');


class Pager2 {

    /**
     * @var	integer	The current page
     */
    public $current_page = null;

    /**
     * @var	integer	The offset that the current page starts at
     */
    public $offset = 0;

    /**
     * @var	integer	The number of items per page
     */
    public $per_page = 10;

    /**
     * @var	integer	The number of total pages
     */
    public $total_pages = 0;

    public $adjacents = 2;

    /**
     * @var array The HTML for the display
     */

    public $template = array(
        'wrapper_start' 	=> '<div class="pagination"> ',
        'wrapper_end'   	=> ' </div>',
        'page_start'		=> '<ul>',
        'page_end'			=> ' </ul>',
        'item_start'		=> '<li>',
        'item_end'			=> ' </li>',
        'disabled_start'	=> '<li class="disabled">',
        'disabled_end'		=> ' </li>',
        'active_start'		=> '<li class="active"> ',
        'active_end'		=> ' </li>',
        'previous_mark'  	=> '&laquo; ',
        'next_mark'      	=> ' &raquo;',
        'previous_text'  	=> 'Previous',
        'next_text'      	=> 'Next',

    );

    /**
     * @var	integer	The total number of items
     */
    public $total_items = 0;

    /**
     * @var	integer	The total number of links to show
     */
    public $num_links = 5;

    /**
     * @var	integer	The URI segment containg page number
     */
    public $uri_segment = 3;

    /**
     * @var	mixed	The pagination URL
     */
    public $pagination_url;

    public $type;

    /**
     * Init
     *
     * Loads in the config and sets the variables
     *
     * @access	public
     * @return	void
     */
    public function _init()
    {
        $config = ci()->config->item('pager', array());

        $this->set_config($config);
    }

    // --------------------------------------------------------------------

    /**
     * Set Config
     *
     * Sets the configuration for pagination
     *
     * @access public
     * @param array   $config The configuration array
     * @return void
     */
    public function set_config(array $config)
    {
        $this->type = "link";
        foreach ($config as $key => $value)
        {
            if ($key == 'template')
            {
                $this->template = array_merge($this->template, $config['template']);
                continue;
            }

            if ($key == "type") $this->type = $value;

            $this->{$key} = $value;

        }


        $this->initialize();
    }

    // --------------------------------------------------------------------

    /**
     * Prepares vars for creating links
     *
     * @access public
     * @return array    The pagination variables
     */
    protected function initialize()
    {
        $this->total_pages = ceil($this->total_items / $this->per_page) ?: 1;

        if($this->type=="ajax"){
            if($this->current_page == null){
                $this->current_page = (int) ci()->input->post("page");

                if (abs($this->current_page)==0) {
                    $this->current_page = (int) ci()->input->get("page");
                }
            }
        }else {
            $this->current_page = (int) ci()->uri->segment($this->uri_segment);
        }

        if ($this->current_page > $this->total_pages)
        {
            $this->current_page = $this->total_pages;
        }
        elseif ($this->current_page < 1)
        {
            $this->current_page = 1;
        }
        // The current page must be zero based so that the offset for page 1 is 0.
        $this->offset = ($this->current_page - 1) * $this->per_page;
    }

    // --------------------------------------------------------------------

    /**
     * Creates the pagination links
     *
     * @access public
     * @return mixed    The pagination links
     */
    public function create_links()
    {
        if ($this->total_pages == 0)
        {
            return '';
        }

        $pagination  = $this->template['wrapper_start'];
        $pagination .= $this->template['page_start'];
        $pagination .= $this->prev_link($this->template['previous_text']);
        $pagination .= $this->page_links($this->type);
        $pagination .= $this->next_link($this->template['next_text']);
        $pagination .= $this->template['page_end'];
        $pagination .= $this->template['wrapper_end'];

        return $pagination;
    }

    // --------------------------------------------------------------------

    /**
     * Pagination Page Number links
     *
     * @access public
     * @return mixed    Markup for page number links
     */
    public function page_links($type)
    {
        if ($this->total_pages == 0)
        {
            return '';
        }

        $pagination = '';

        // Let's get the starting page number, this is determined using num_links
        $start = (($this->current_page - $this->num_links) > 0) ? $this->current_page - ($this->num_links - 1) : 1;

        // Let's get the ending page number
        $end = $this->total_pages;


        if ($end < 5 + ($this->adjacents * 2))
        {
            // not enough pages to break it up
            for($i = $start; $i <= $end; $i++)
            {
                if ($this->current_page == $i)
                {
                    $pagination .= $this->template['active_start'].anchor('#', $i, array('onclick'=>'return false;','class'=>'current_page btn')).$this->template['active_end'];
                }
                else
                {
                    $url = ($i == 1) ? '' : '/'.$i;
                    if($type=="ajax"){
                        $pagination .= $this->template['item_start']."<a href='#' class='goto_page btn' page='{$i}' >{$i}</a>".$this->template['item_end'];
                    }else{
                        $pagination .= $this->template['item_start'].anchor(rtrim($this->pagination_url, '/').$url, $i).$this->template['item_end'];
                    }
                }
            }

        }
        elseif ($end >= 5 + ($this->adjacents * 2))
        {
            // enough pages to hide some

            if ($this->current_page < 2 + ($this->adjacents * 2))
            {
                // current page is somewhere in the beginning
                for ($i = 1; $i < 3 + ($this->adjacents * 2); $i++)
                {
                    if ($this->current_page == $i)
                    {
                        $pagination .= $this->template['active_start'].anchor('#', $i, array('onclick'=>'return false;','class'=>'current_page btn')).$this->template['active_end'];
                    }
                    else
                    {
                        $url = ($i == 1) ? '' : '/'.$i;
                        if($type=="ajax"){
                            $pagination .= $this->template['item_start']."<a href='#' class='goto_page btn' page='{$i}' >{$i}</a>".$this->template['item_end'];
                        }else{
                            $pagination .= $this->template['item_start'].anchor(rtrim($this->pagination_url, '/').$url, $i).$this->template['item_end'];
                        }
                    }
                }
                $pagination .= $this->template['disabled_start'].anchor('#', '...', array('onclick'=>'return false;')).$this->template['disabled_end'];

                if($type=="ajax"){
                    $pagination .= $this->template['item_start']."<a href='#' class='goto_page btn' page='".($end-1)."' >".($end-1)."</a>".$this->template['item_end'];
                    $pagination .= $this->template['item_start']."<a href='#' class='goto_page btn' page='".($end)."' >".($end)."</a>".$this->template['item_end'];
                }else{
                    $pagination .= $this->template['item_start'].anchor(rtrim($this->pagination_url, '/').'/'.($end-1), ($end-1)).$this->template['item_end'];
                    $pagination .= $this->template['item_start'].anchor(rtrim($this->pagination_url, '/').'/'.$end, $end).$this->template['item_end'];
                }
            }
            elseif ($end - ($this->adjacents * 2) - 1 >= $this->current_page && $this->current_page >= ($this->adjacents * 2))
            {
                // current page somewhere in the middle
                if($type=="ajax"){
                    $pagination .= $this->template['item_start']."<a href='#' class='goto_page btn' page='1' >1</a>".$this->template['item_end'];
                    $pagination .= $this->template['item_start']."<a href='#' class='goto_page btn' page='2' >2</a>".$this->template['item_end'];
                }else{
                    $pagination .= $this->template['item_start'].anchor(rtrim($this->pagination_url, '/').'/'.'1', '1').$this->template['item_end'];
                    $pagination .= $this->template['item_start'].anchor(rtrim($this->pagination_url, '/').'/'.'2', '2').$this->template['item_end'];
                }
                $pagination .= $this->template['disabled_start'].anchor('#', '...', array('onclick'=>'return false;')).$this->template['disabled_end'];
                for ($i = $this->current_page - $this->adjacents + 1; $i < $this->current_page + $this->adjacents; $i++)
                {
                    if ($this->current_page == $i)
                    {
                        $pagination .= $this->template['active_start'].anchor('#', $i, array('onclick'=>'return false;','class'=>'current_page btn')).$this->template['active_end'];
                    }
                    else
                    {
                        if($type=="ajax"){
                            $pagination .= $this->template['item_start']."<a href='#' class='goto_page btn' page='{$i}' >{$i}</a>".$this->template['item_end'];
                        }else{
                            $url = ($i == 1) ? '' : '/'.$i;
                            $pagination .= $this->template['item_start'].anchor(rtrim($this->pagination_url, '/').$url, $i).$this->template['item_end'];
                        }
                    }
                }
                $pagination .= $this->template['disabled_start'].anchor('#', '...', array('onclick'=>'return false;')).$this->template['disabled_end'];
                if($type=="ajax"){
                    $pagination .= $this->template['item_start']."<a href='#' class='goto_page btn' page='".($end-1)."' >".($end-1)."</a>".$this->template['item_end'];
                    $pagination .= $this->template['item_start']."<a href='#' class='goto_page btn' page='".($end)."' >".($end)."</a>".$this->template['item_end'];
                }else{
                    $pagination .= $this->template['item_start'].anchor(rtrim($this->pagination_url, '/').'/'.($end-1), ($end-1)).$this->template['item_end'];
                    $pagination .= $this->template['item_start'].anchor(rtrim($this->pagination_url, '/').'/'.$end, $end).$this->template['item_end'];
                }
            }
            else
            {
                // current page is somewhere in the end
                if($type=="ajax"){
                    $pagination .= $this->template['item_start']."<a href='#' class='goto_page btn' page='1' >1</a>".$this->template['item_end'];
                    $pagination .= $this->template['item_start']."<a href='#' class='goto_page btn' page='2' >2</a>".$this->template['item_end'];
                }else{
                    $pagination .= $this->template['item_start'].anchor(rtrim($this->pagination_url, '/').'/'.'1', '1').$this->template['item_end'];
                    $pagination .= $this->template['item_start'].anchor(rtrim($this->pagination_url, '/').'/'.'2', '2').$this->template['item_end'];
                }
                $pagination .= $this->template['disabled_start'].anchor('#', '...', array('onclick'=>'return false;')).$this->template['disabled_end'];
                for ($i = $end - ($this->adjacents * 2) - 1; $i <= $end; $i++)
                {
                    if ($this->current_page == $i)
                    {
                        $pagination .= $this->template['active_start'].anchor('#', $i, array('onclick'=>'return false;','class'=>'current_page btn')).$this->template['active_end'];
                    }
                    else
                    {
                        if($type=="ajax"){
                            $pagination .= $this->template['item_start']."<a href='#' class='goto_page btn' page='{$i}' >{$i}</a>".$this->template['item_end'];
                        }else{
                            $url = ($i == 1) ? '' : '/'.$i;
                            $pagination .= $this->template['item_start'].anchor(rtrim($this->pagination_url, '/').$url, $i).$this->template['item_end'];
                        }
                    }
                }
            }
        }

        return $pagination;
    }

    // --------------------------------------------------------------------

    /**
     * Pagination "Next" link
     *
     * @access public
     * @param string $value The text displayed in link
     * @return mixed    The next link
     */
    public function next_link($value)
    {
        if ($this->total_pages == 0)
        {
            return '';
        }

        if ($this->current_page == $this->total_pages)
        {
            return $this->template['disabled_start'].anchor('#', $value.$this->template['next_mark'], array('onclick'=>'return false;','class'=>'btn btn-next')).$this->template['disabled_end'];
        }
        else
        {
            $next_page = $this->current_page + 1;
            if($this->type=="ajax"){
                return $this->template['item_start']."<a href='#' class='goto_page btn btn-next' page='{$next_page}' >".$value.$this->template['next_mark']."</a>".$this->template['item_end'];
            }else{
                return $this->template['item_start'].anchor(rtrim($this->pagination_url, '/').'/'.$next_page, $value.$this->template['next_mark']).$this->template['item_end'];
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Pagination "Previous" link
     *
     * @access public
     * @param string $value The text displayed in link
     * @return mixed    The previous link
     */
    public function prev_link($value)
    {
        if ($this->total_pages == 0)
        {
            return '';
        }

        if ($this->current_page == 1)
        {
            return $this->template['disabled_start'].anchor('#', $this->template['previous_mark'].$value, array('onclick'=>'return false;','class'=>'btn btn-prev')).$this->template['disabled_end'];
        }
        else
        {
            $previous_page = $this->current_page - 1;
            if($this->type=="ajax"){
                return $this->template['item_start']."<a href='#' class='goto_page btn btn-prev' page='{$previous_page}' >".$this->template['previous_mark'].$value."</a>".$this->template['item_end'];
            }else{
                $previous_page = ($previous_page == 1) ? '' : '/'.$previous_page;
                return $this->template['item_start'].anchor(rtrim($this->pagination_url, '/').$previous_page, $this->template['previous_mark'].$value).$this->template['item_end'];
            }
        }
    }
}


