///*
// * GBS-VitalC Genealogy javascript 
// *
// */
//
//
///**
// * jQuery.fn.sortElements
// * --------------
// * @author James Padolsey (http://james.padolsey.com)
// * @version 0.11
// * @updated 18-MAR-2010
// * --------------
// * @param Function comparator:
// *   Exactly the same behaviour as [1,2,3].sort(comparator)
// *   
// * @param Function getSortable
// *   A function that should return the element that is
// *   to be sorted. The comparator will run on the
// *   current collection, but you may want the actual
// *   resulting sort to occur on a parent or another
// *   associated element.
// *   
// *   E.g. $('td').sortElements(comparator, function(){
// *      return this.parentNode; 
// *   })
// *   
// *   The <td>'s parent (<tr>) will be sorted instead
// *   of the <td> itself.
// */
//jQuery.fn.sortElements = (function(){
//
//    var sort = [].sort;
//
//    return function(comparator, getSortable) {
//
//        getSortable = getSortable || function(){return this;};
//
//        var placements = this.map(function(){
//
//            var sortElement = getSortable.call(this),
//                parentNode = sortElement.parentNode,
//
//                // Since the element itself will change position, we have
//                // to have some way of storing it's original position in
//                // the DOM. The easiest way is to have a 'flag' node:
//                nextSibling = parentNode.insertBefore(
//                    document.createTextNode(''),
//                    sortElement.nextSibling
//                );
//
//            return function() {
//
//                if (parentNode === this) {
//                    throw new Error(
//                        "You can't sort elements if any one is a descendant of another."
//                    );
//                }
//
//                // Insert before flag:
//                parentNode.insertBefore(this, nextSibling);
//                // Remove flag:
//                parentNode.removeChild(nextSibling);
//
//            };
//
//        });
//
//        return sort.call(this, comparator).each(function(i){
//            placements[i].call(getSortable.call(this));
//        });
//
//    };
//
//})();
//
//(function() {
//	
//	/*
//	 * Initialize the genealogy object
//	 */
//	var root = this;
//	
//	var genealogy = {};
//	
//	if (typeof exports !== 'undefined') {
//		if (typeof module !== 'undefined' && module.exports) {
//			exports = module.exports = genealogy;
//		}
//		exports.genealogy = genealogy;
//		exports.b = genealogy;
//	} else {
//		root['genealogy'] = genealogy;
//		root['g'] = genealogy;
//	}
//	
//	genealogy.req = {};
//	genealogy.req.target_id = '';
//	genealogy.req.url = '';
//	genealogy.req.search_url = '';
//	genealogy.req.downline_url = '';
//	genealogy.req.downline_handler = null;
//	genealogy.req.account_id = '';
//	genealogy.ref = {};
//	genealogy.ref.members = {};
//	genealogy.ref.accounts = {};
//	genealogy.account = {};
//	genealogy.primary_account = {};
//	genealogy.hover = {};
//	genealogy.hover.el = null;
//	genealogy.hover.in_timer = 0;
//	genealogy.hover.out_timer = 0;
//	genealogy.hover.account_id = '';
//	genealogy.hover.member_id = '';
//	genealogy.templates = {};
//	genealogy.templates.popover = "<div id='genealogy-popover-info' class='genealogy-popup'> \
//		<div class='genealogy-arrow genealogy-arrow-left'></div> \
// 		<div class='genealogy-arrow genealogy-arrow-right'></div> \
//		<div class='genealogy-popup-container'> \
//			<button class='close btn-close-genealogy-popover'><i class='icon-remove icon-white'></i></button> \
//			<span class='genealogy-info'><span title='<%= fullname %>'><%= fullname %></span> (<%= account_id %>)</span> \
//			<span class='genealogy-info'><abbr title='Upline'>U:</abbr> <span title='<%= upline_fullname %>'><%= upline_fullname %></span> (<%= upline_id %>)</span> \
//			<span class='genealogy-info'><abbr title='Sponsor'>S:</abbr> <span title='<%= sponsor_fullname %>'><%= sponsor_fullname %></span> (<%= sponsor_id %>)</span> \
//			<div class='genealogy-action'> \
//				<button class='btn btn-super-tiny btn-genealogy-network'><i class='icon-search'></i> View Network &nbsp;</button> \
//				<button class='btn btn-super-tiny btn-genealogy-message' data-member-account-id='<%= member_account_id %>' data-member-fullname='<%= propername %>'><i class='icon-search'></i> Send Private Message &nbsp;</button> \
//			</div> \
//		</div> \
//	</div>";
//
//	genealogy.templates.emptyPopover = "<div id='genealogy-popover-info' class='genealogy-popup'> \
//		<div class='genealogy-arrow genealogy-arrow-left'></div> \
// 		<div class='genealogy-arrow genealogy-arrow-right'></div> \
//		<div class='genealogy-popup-container'> \
//			<button class='close btn-close-genealogy-popover'><i class='icon-remove icon-white'></i></button> \
//			<span class='genealogy-info' style='font-size: 18px;line-height: 50px; text-align:center;'>Available Slot</span> \
//			<div class='genealogy-action'> \
//				<button class='btn btn-super-tiny btn-add-new-account' data-parent-account-id='<%= parent_account_id %>' data-direction='<%= direction %>' ><i class='icon-plus-sign'></i> Add Account &nbsp;</button> \
//				<button class='btn btn-super-tiny btn-add-new-distributor' data-parent-account-id='<%= parent_account_id %>' data-direction='<%= direction %>' ><i class='icon-plus-sign'></i> Add Distributor &nbsp;</button> \
//			</div> \
//		</div> \
//	</div>";
//	
//	genealogy.templates.network = "  \
//		<div class='genealogy-legend-box'> \
//			<div class='clearfix'><div class='node-legend-box node-available'></div><span> - Available</span></div> \
//			<div class='clearfix'><div class='node-legend-box node-cd'></div><span> - CD</span></div> \
//			<div class='clearfix'><div class='node-legend-box node-sp'></div><span> - SP</span></div> \
//			<div class='clearfix'><div class='node-legend-box node-vp'></div><span> - VP</span></div> \
//			<div class='clearfix'><div class='node-legend-box node-tp'></div><span> - TP</span></div> \
//			<div class='clearfix'><div class='node-legend-box node-fs'></div><span> - FS</span></div> \
//			<div class='clearfix'><div class='node-legend-box node-fs-rhm'></div><span> - FS-RHM</span></div> \
//			<div class='clearfix'><div class='node-legend-box node-maint'>M</div><span> - No Monthly Maint.</span></div> \
//			<div class='clearfix'><div class='node-legend-box node-maint'>A</div><span> - No Annual Maint.</span></div> \
//		</div> \
//		<div class='genealogy-button-box'> \
//			<button class='btn btn-success btn-small btn-genealogy-move btn-genealogy-move-up'>Move 1 Level Up</button> \
//			<button class='btn btn-success btn-small btn-genealogy-move btn-genealogy-root'>Move to Top</button> \
//			<button class='btn btn-info btn-small btn-genealogy-search'><i class='icon-search icon-white'></i>&nbsp;Search</button> \
//		</div> \
//		<div class='genealogy-level level1'> \
//			<div data-node='0' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//		</div> \
//		<div class='genealogy-level level2'> \
//			<div data-node='2' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='1' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//		</div> \
//		<div class='genealogy-level level3'> \
//			<div data-node='22' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='21' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='12' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='11' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//		</div> \
//		<div class='genealogy-level level4'> \
//			<div data-node='222' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='221' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
// 			</div> \
//			<div data-node='212' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='211' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='122' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='121' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='112' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='111' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//		</div> \
//		<div class='genealogy-level level5'> \
//			<div data-node='2222' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='2221' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='2212' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='2211' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='2122' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='2121' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='2112' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='2111' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='1222' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='1221' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='1212' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='1211' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='1122' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='1121' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='1112' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//			<div data-node='1111' class='node-box'> \
//				<div class='center-line top'></div> \
//				<div class='center-line bottom'></div> \
//				<div class='bottom-line'></div> \
//				<div class='node-content'></div> \
//				<div class='node-extra-legend'> \
//					<span class='maint-legend maint-monthly'>M</span> \
//					<span class='maint-legend maint-annual'>A</span> \
//				</div> \
//			</div> \
//		</div> ";
//	
//	genealogy.templates.network_list_container = "<div class='g-table-mem-list' > \
//		<table class='table table-bordered table-condensed '> \
//			<thead> \
//				<tr> \
//					<th class='g-table-account-id' >Account ID</th> \
//					<th >Account Name</th> \
//					<th class='g-table-account-id' >Upline</th> \
//					<th class='g-table-pos' >Pos</th> \
//					<th class='g-table-dt' >Date Registered</th> \
//					<th class='g-table-type' >Type</th> \
//					<th class='g-table-count' >L</th> \
//					<th class='g-table-count' >R</th> \
//				</tr> \
//			</thead> \
//			<tbody class='g-table-mem-items'> \
//				<tr><td colspan='8'>Loading... <img src='/assets/img/loading2.gif' alt='' style='height:55%;' /></td></tr> \
//			</tbody> \
//		</table> \
//		<div class='g-table-mem-pager'></div> \
//	</div>";
//	
//	genealogy.templates.network_list_item = " \
//			<% \
//				$.each(accounts, function(index, item) { \
//						var _mem = genealogy.ref.members[item.member_id]; \
//						var _fullname = (_mem.first_name + ' ' + _mem.last_name).toUpperCase(); \
//						var _type = ''; \
//						if (item.account_type_id == 1) \
//							_type = 'SP'; \
//						else if (item.account_type_id == 2) \
//							_type = 'VP'; \
//						else if (item.account_type_id == 3) \
//							_type = 'TP'; \
//						else if (item.account_type_id == 4) \
//							_type = 'CD'; \
//						else if (item.account_type_id == 5) \
//							_type = 'FS'; \
//						var _pos = item.side == '2' ? 'L' : 'R'; \
//			%> \
//				<tr data-node-address='<%= item.node_address %>' data-account-id='<%= item.account_id %>'> \
//					<td class='g-table-account-id' ><%= item.account_id %></td> \
//					<td ><%= _fullname %></td> \
//					<td class='g-table-account-id' ><%= item.upline_id %></td> \
//					<td class='g-table-pos' ><%= _pos %></td> \
//					<td class='g-table-dt' ><%= item.insert_timestamp %></td> \
//					<td class='g-table-type' ><%= _type %></td> \
//					<td class='g-table-count left-side' ><img src='/assets/img/loading2.gif' alt='' style='width:40%;' /></td> \
//					<td class='g-table-count right-side' ><img src='/assets/img/loading2.gif' alt='' style='width:40%;' /></td> \
//				</tr> \
//			<% \
//				}); \
//			%> \
//	";
//	
//	genealogy.templates.search_result = "<form id='frm_g_member_search' class='form-search' onsubmit='return false;' > \
//	            <input id='g_search_key' name='g_search_key' type='text' class='input-xxlarge search-query' placeholder='Enter Account ID or Name' value='' /> \
//	            <button id='btn_g_search' class='btn'><i class='icon-search'></i>&nbsp;Search</button> \
//	          </form> \
//	<table class='table table-striped table-condensed g-member-list'> \
//		<thead> \
//			<tr> \
//				<th class='account_number'>Account ID</th> \
//				<th class='level_away'>Lvl Down</th> \
//				<th>Name</th> \
//				<th style='width:150px;'>Member Type</th> \
//				<th class='action'>&nbsp;</th> \
//			</tr> \
//		</thead> \
//	</table> \
//	<div id='g-member-list'> \
//		<table class='table table-striped table-condensed g-member-list'> \
//			<tbody id='g-member-listing'> \
//			</tbody> \
//		</table> \
//	</div>";
//	
//	genealogy.templates.search_result_item = "<% $.each(members, function(index, item) { %> \
//			<tr id='cust_<%= item.account_id %>'> \
//				<td class='account_number'><%= item.account_id %></td> \
//				<td class='level_away'><%= item.level_away %></td> \
//				<td><%= item.fullname %></td> \
//				<td><%= item.member_type %></td> \
//				<td class='action'><button class='btn btn-small btn-info btn_g_select_member' data-id='<%= item.account_id %>'>Select</button></td> \
//			</tr> \
//	<%	}); %>";
//		
//	genealogy.reset = function() {
//		genealogy.req.target_id = '';
//		genealogy.req.url = '';
//		genealogy.req.search_url = '';
//		genealogy.req.downline_url = '';
//		genealogy.req.downline_handler = null;
//		genealogy.req.account_id = '';
//		genealogy.ref.members = {};
//		genealogy.ref.accounts = {};
//		genealogy.network = {};
//	};
//	
//	genealogy.get = function(options, cb, error_cb) {
//		if (typeof(options) == 'undefined') return;
//		if (typeof(options.url) == 'undefined') return;
//		if (typeof(options.account_id) == 'undefined') return;
//		
//		genealogy.req.url = options.url;
//		genealogy.req.account_id = options.account_id;
//		$('#'+genealogy.req.target_id+' .genealogy-container .genealogy-tree-container').html("<div style='margin:10px;'>Loading... <img src='/assets/img/loading2.gif' alt='' /></div>");
//		
//		// cancel downline count request
//		if (_.isObject(genealogy.req.downline_handler))
//			if (typeof(genealogy.req.downline_handler.abort) != 'undefined') genealogy.req.downline_handler.abort();
//		b.request({
//			with_overlay: false,
//			url: genealogy.req.url,
//			data: {'account_id' : genealogy.req.account_id},
//			on_success: function(data, status) {
//				if (data.status == 'ok') {
//					genealogy.account = data.data.account;
//					
//					if (_.size(genealogy.primary_account) == 0) {
//						genealogy.primary_account = _.clone(genealogy.account);
//					}
//					
//					genealogy.ref.members = data.data.members;
//					genealogy.ref.accounts = data.data.accounts;
//					if (_.isFunction(cb)) cb.call(this);
//				} else {
//					if (_.isFunction(error_cb)) error_cb.call(this, data);
//				}
//			}
//		});
//	};
//	
//	genealogy.displayNetwork = function() {
//	
//		if (typeof(genealogy.account.network) == 'undefined') return;
//		$('[data-node="0"]').data('account-id', genealogy.account.account_id);
//		$('[data-node="0"]').data('member-id', genealogy.account.member_id);
//		$('[data-node="0"]').removeClass('blank');
//		if (genealogy.account.account_type_id == 1)
//			$('[data-node="0"]').addClass('node-sp');
//		else if (genealogy.account.account_type_id == 2)
//			$('[data-node="0"]').addClass('node-vp');
//		else if (genealogy.account.account_type_id == 3)
//			$('[data-node="0"]').addClass('node-tp');
//		else if (genealogy.account.account_type_id == 4)
//			$('[data-node="0"]').addClass('node-cd');
//		else if (genealogy.account.account_type_id == 5)
//			$('[data-node="0"]').addClass('node-fs')
//		else if (genealogy.account.account_type_id == 8)
//			$('[data-node="0"]').addClass('node-fs-rhm');
//
//		$('[data-node="0"] .node-content').html("<img src='/media/profile/"+genealogy.account.member_id+".jpg?v=" + (new Date().getTime()) + "' alt='' />");
//
//		// show maint legends
//		if (typeof(genealogy.account.ms_monthly_maintenance_ctr) != 'undefined')
//			if (parseInt(genealogy.account.ms_monthly_maintenance_ctr) < 2) {
//				$('[data-node="0"] .node-extra-legend .maint-monthly').show();
//			}
//
//		if (typeof(genealogy.account.ms_annual_maintenance_ctr) != 'undefined')
//			if (parseInt(genealogy.account.ms_annual_maintenance_ctr) < 4) {
//				$('[data-node="0"] .node-extra-legend .maint-annual').show();
//			}
//				
//		$.each(genealogy.account.network, function(index, item) {
//			$('[data-node="'+item.node_address+'"]').data('account-id', item.account_id);
//			$('[data-node="'+item.node_address+'"]').data('member_id-id', item.member_id);
//			$('[data-node="'+item.node_address+'"]').removeClass('blank');
//			if (item.account_type_id == 1)
//				$('[data-node="'+item.node_address+'"]').addClass('node-sp');
//			else if (item.account_type_id == 2)
//				$('[data-node="'+item.node_address+'"]').addClass('node-vp');
//			else if (item.account_type_id == 3)
//				$('[data-node="'+item.node_address+'"]').addClass('node-tp');
//			else if (item.account_type_id == 4)
//				$('[data-node="'+item.node_address+'"]').addClass('node-cd');
//			else if (item.account_type_id == 5)
//				$('[data-node="'+item.node_address+'"]').addClass('node-fs');
//			else if (item.account_type_id == 8)
//				$('[data-node="'+item.node_address+'"]').addClass('node-fs-rhm');	
//
//			$('[data-node="'+item.node_address+'"] .node-content').html("<img src='/media/profile/"+item.member_id+".jpg?v=" + (new Date().getTime()) + "' alt='' />");
//
//			// show maint legends
//			if (typeof(item.ms_monthly_maintenance_ctr) != 'undefined')
//				if (parseInt(item.ms_monthly_maintenance_ctr) < 2) {
//					$('[data-node="'+item.node_address+'"] .node-extra-legend .maint-monthly').show();
//				}
//
//			if (typeof(item.ms_annual_maintenance_ctr) != 'undefined')
//				if (parseInt(item.ms_annual_maintenance_ctr) < 4) {
//					$('[data-node="'+item.node_address+'"] .node-extra-legend .maint-annual').show();
//				}
//		});
//		
//		var _node_address = [];
//		$.each($('.node-box.blank'), function(index, item) {
//			var node_address = $(item).data('node');
//			node_address = String(node_address);
//			var parent_node_address = node_address.substring(0, node_address.length-1);
//			if (parent_node_address.length == 0) parent_node_address = '0';
//
//			if ($('[data-node="'+parent_node_address+'"]').hasClass('blank') == false) {
//				_node_address.push(node_address);
//			}
//		});
//		
//		$.each(_node_address, function(index, item) {
//			$('[data-node="'+item+'"]').removeClass('blank');
//			$('[data-node="'+item+'"]').addClass('node-available');
//		});
//	};
//	
//	genealogy.renderPopover = function(el, account_id ) {
//		
//		if (typeof(genealogy.ref.accounts[account_id]) == 'undefined') return;
//		
//		var _account = genealogy.ref.accounts[account_id];
//		var _upline = genealogy.ref.accounts[_account.upline_id];
//		var _sponsor = genealogy.ref.accounts[_account.sponsor_id];
//		
//		var data = {
//			'fullname' : String(genealogy.ref.members[_account.member_id].first_name + ' ' + genealogy.ref.members[_account.member_id].last_name).toUpperCase(),
//			'account_id' : account_id,
//			'upline_fullname' : String(genealogy.ref.members[_upline.member_id].first_name + ' ' + genealogy.ref.members[_upline.member_id].last_name).toUpperCase(),
//			'upline_id' : _account.upline_id,
//			'sponsor_fullname' : String(genealogy.ref.members[_sponsor.member_id].first_name + ' ' + genealogy.ref.members[_sponsor.member_id].last_name).toUpperCase(),
//			'sponsor_id' : _account.sponsor_id,
//            'member_account_id' : account_id,
//            'propername' : String(genealogy.ref.members[_account.member_id].first_name + ' ' + genealogy.ref.members[_account.member_id].last_name).toUpperCase(),
//		};
//		
//		var html = _.template(genealogy.templates.popover, data);
//		
//		// remove existing popover
//		$('#genealogy-popover-info').remove();
//		var $popover = $(html);
//		
//		var _pos = $(el).position();
//		var _l_m = $(el).css('margin-left');
//		var _p_l_m = $(el).parent().css('margin-left');
//		var _p_pos  = $(el).parent().position();
//		var _w = $(el).outerWidth();
//		var _offset = 2;
//		
//		_l_m = parseInt(String(_l_m).replace('px', ''));
//		_p_l_m = parseInt(String(_p_l_m).replace('px', ''));
//		
//		var _t = _pos.top + _p_pos.top;
//		var _l = _pos.left + _p_pos.left + _l_m + _p_l_m + _offset;
//		var _total_w = 280;
//		
//		var _arrow = 'l';			
//		if (_l+_total_w > 700) {
//			_l = _l - (282  + _offset);
//			_arrow = 'r';
//		}
//		 
//		$popover.css({'top' : _t, 'left' : _l});
//		
//		// put it in
//		$('.genealogy-container').append($popover);
//		
//		$popover.find('.genealogy-arrow').hide();
//		if (_arrow == 'l') {
//			$popover.find('.genealogy-popup-container').addClass('left');
//			$popover.find('.genealogy-arrow-left').show();
//		} else {
//			$popover.find('.genealogy-arrow-right').show();
//		}
//			
//		$popover.show();
//		
//		if (el.data('node') == '0')
//			$('.btn-genealogy-network').hide();
//		
//		if (account_id == genealogy.primary_account.account_id)
//			$('.btn-genealogy-message').hide();
//		
//	};
//	
//	genealogy.renderEmptyPopover = function(el, parent_account_id, direction ) {
//		
//		var html = _.template(genealogy.templates.emptyPopover, {'parent_account_id' : parent_account_id, 'direction' : direction});
//		
//		// remove existing popover
//		$('#genealogy-popover-info').remove();
//		var $popover = $(html);
//		
//		var _pos = $(el).position();
//		var _l_m = $(el).css('margin-left');
//		var _p_l_m = $(el).parent().css('margin-left');
//		var _p_pos  = $(el).parent().position();
//		var _w = $(el).outerWidth();
//		var _offset = 2;
//		
//		_l_m = parseInt(String(_l_m).replace('px', ''));
//		_p_l_m = parseInt(String(_p_l_m).replace('px', ''));
//		
//		var _t = _pos.top + _p_pos.top;
//		var _l = _pos.left + _p_pos.left + _l_m + _p_l_m + _offset;
//		var _total_w = 280;
//		
//		var _arrow = 'l';			
//		if (_l+_total_w > 700) {
//			_l = _l - (282  + _offset);
//			_arrow = 'r';
//		}
//		 
//		$popover.css({'top' : _t, 'left' : _l});
//		
//		// put it in
//		$('.genealogy-container').append($popover);
//		
//		$popover.find('.genealogy-arrow').hide();
//		if (_arrow == 'l') {
//			$popover.find('.genealogy-popup-container').addClass('left');
//			$popover.find('.genealogy-arrow-left').show();
//		} else {
//			$popover.find('.genealogy-arrow-right').show();
//		}
//			
//		$popover.show();
//		
//	};
//	
//	genealogy.render = function(options) {
//		
//		$('#genealogy-popover-info').remove();
//		
//		if (typeof(options) == 'undefined') return;
//		if (typeof(options.target_id) == 'undefined') return;
//		if (typeof(options.url) == 'undefined') return;
//		if (typeof(options.account_id) == 'undefined') return;
//		
//		genealogy.req.target_id = options.target_id;
//		
//		if (typeof(options.search_url) != 'undefined')
//			genealogy.req.search_url = options.search_url;
//			
//		if (typeof(options.downline_url) != 'undefined')
//			genealogy.req.downline_url = options.downline_url;
//			
//		if ($('#'+genealogy.req.target_id+' .genealogy-container').length == 0)
//			$('#'+genealogy.req.target_id).html("<div class='genealogy-container'><div class='genealogy-tree-container'></div>"+genealogy.templates.network_list_container+"</div>");
//		
//		genealogy.get({
//			'url' : options.url, 
//			'account_id' : options.account_id
//		}, function() {
//			
//			var html = _.template(genealogy.templates.network, {});
//			var $network = $(html);
//			$network.find('.node-box').addClass('blank');
//			if (genealogy.primary_account.account_id != genealogy.account.account_id) {
//				$network.find('.btn-genealogy-move').show();
//			}
//			$('#'+options.target_id+' .genealogy-container .genealogy-tree-container').html($network);
//			genealogy.displayNetwork();
//
//			genealogy.get_downline();
//			
//			if (_.isFunction(options.on_success)) options.on_success.call(this);
//		}, options.on_error);
//		
//	};
//	
//	
//	genealogy.get_downline = function(page) {
//		
//		page = typeof(page) == 'undefined' ? 1 : page;
//		
//		$('.g-table-mem-items').html("<tr><td colspan='8'>Loading... <img src='/assets/img/loading2.gif' alt='' style='height:55%;' /></td></tr>");
//		
//		// get donwline 
//		genealogy.req.downline_handler = b.request({
//			with_overlay: false,
//			url: genealogy.req.downline_url + '/' + page,
//			data: {'account_id' : genealogy.account.account_id},
//			on_success: function(data, status) {
//				if (data.status == 'ok') {
//					var _accounts = data.data.accounts;
//					var _members = data.data.members;
//					
//					$.each(_members, function(index, item) {
//						if (typeof(genealogy.ref.members[index]) == 'undefined')
//							genealogy.ref.members[index] = _.clone(item);
//					});
//					
//					var _account_ids = [];
//					$.each(_accounts, function(index, item) {
//						_account_ids.push(item.account_id);
//					});
//					
//					$('.g-table-mem-items').html(_.template(genealogy.templates.network_list_item, {'accounts' : _accounts}));
//					$('.g-table-mem-pager').html(data.data.pager);
//					
//					// get downline counts
//					genealogy.req.downline_handler = b.request({
//						with_overlay: false,
//						url: genealogy.req.downline_url+'_count',
//						data: {'account_ids' : _account_ids},
//						on_success: function(data, status) {
//							var _accounts = data.data;
//							$.each(_accounts, function(index, item) {
//								$("tr[data-account-id='"+index+"'] td.left-side").html(item.left);
//								$("tr[data-account-id='"+index+"'] td.right-side").html(item.right);
//							});
//						}
//					});
//					
//				}
//			}
//		});
//		
//	};
//	
//	genealogy.search = function() {
//		
//		var modal = b.modal.create({
//			title: "Member Search",
//			html: _.template(genealogy.templates.search_result, {}),
//			width: 700
//		});
//		modal.show();
//		
//		$('#btn_g_search').click(function(e) {
//			e.preventDefault();
//			
//			var _search_key = $.trim($('#g_search_key').val());
//			
//			if (_search_key.length == 0 ) return false;
//			
//			b.request({
//				url: genealogy.req.search_url,
//				data: {'root_account_id' : genealogy.primary_account.account_id, 'search_key' : _search_key},
//				on_success: function(data, status) {
//
//					if (data.status == 'ok') {
//						var member_accounts = data.data;
//						$('#g-member-listing').html(_.template(genealogy.templates.search_result_item, {'members' : member_accounts}));
//						$('#g-member-listing td:nth-child(3)').highlight(_search_key);
//					} else {
//						$('#g-member-listing').html("<td>"+data.msg+"</td>");
//					}
//				}
//			});
//			
//		});
//		
//		$("body").undelegate(".btn_g_select_member", "click");
//		$("body").delegate(".btn_g_select_member", "click", function(e) {
//			e.preventDefault();
//			
//			var _acct_id = $.trim($(this).data('id'));
//			if (_acct_id.length == 0) return false;
//			genealogy.render({
//				'target_id' : genealogy.req.target_id,
//				'url' : genealogy.req.url,
//				'account_id' : _acct_id
//			});
//			
//			modal.hide();
//			
//		});
//		
//		
//		
//	};
//	
//	
//}).call(this);
//
//$("body").on("click", ".node-content", function(e) {
//	var $el = $(this).parent();
//	clearTimeout(genealogy.hover.in_timer);
//	clearTimeout(genealogy.hover.out_timer);
//	genealogy.hover.in_timer = setTimeout(function() {
//		genealogy.hover.el = $el;
//		genealogy.hover.account_id = genealogy.hover.el.data('account-id');
//		genealogy.hover.member_id = genealogy.hover.el.data('member-id');
//		genealogy.hover.node = genealogy.hover.el.data('node');
//		
//		if (typeof(genealogy.hover.account_id) == 'undefined') genealogy.hover.account_id = '';
//		if (typeof(genealogy.hover.member_id) == 'undefined') genealogy.hover.member_id = '';
//		if (genealogy.hover.account_id != '') {
//			genealogy.renderPopover(genealogy.hover.el, genealogy.hover.account_id);
//		} else {
//			if (genealogy.hover.el.hasClass('node-available')) {
//				var parent_account_id = 0;
//				var node_address = String(genealogy.hover.el.data('node'));
//				var direction = node_address[node_address.length-1];
//				if (node_address.length == 1) {
//					parent_account_id = $('[data-node="0"]').first().data('account-id');
//				} else {
//					parent_account_id = $('[data-node="'+node_address.substring(0, node_address.length-1)+'"]').first().data('account-id');
//				}
//				genealogy.renderEmptyPopover(genealogy.hover.el, parent_account_id, direction);
//			}
//		}
//		
//	},150);
//
//});
//
//
//$("body").on("mouseleave", ".node-box", function(e){
//
//	if ($('#genealogy-popover-info').length == 0) {
//
//		clearTimeout(genealogy.hover.in_timer);
//		
//		// clear values
//		genealogy.hover.el = null;
//		genealogy.hover.timer = 0;
//		genealogy.hover.account_id = '';
//		genealogy.hover.member_id = '';
//		genealogy.hover.node = '';
//	}
//
//
//});
//
//$("body").on("mouseleave", ".genealogy-popup", function(e){
//
//	genealogy.hover.out_timer = setTimeout(function() {
//		$('#genealogy-popover-info').remove();
//		
//		// clear values
//		genealogy.hover.el = null;
//		genealogy.hover.timer = 0;
//		genealogy.hover.account_id = '';
//		genealogy.hover.member_id = '';
//		genealogy.hover.node = '';
//		
//	},250);
//	
//	clearTimeout(genealogy.hover.in_timer);
//
//});
//
//$("body").on("click", ".btn-genealogy-network", function(e) {
//	e.preventDefault();
//	
//	if (genealogy.hover.account_id.length > 0) {
//		genealogy.render({
//			'target_id' : genealogy.req.target_id,
//			'url' : genealogy.req.url,
//			'account_id' : genealogy.hover.account_id
//		});
//	}
//});
//
//$("body").on("click", ".btn-genealogy-message", function(e) {
//	e.preventDefault();
//	
//    var member_account_id = $(this).data('member-account-id');
//    var member_fullname = $(this).data('member-fullname');
//    $('#'+genealogy.req.target_id).trigger('on-send-private-message', [member_account_id, member_fullname]);
//
//});
//
//$("body").on("click", ".btn-add-new-account", function(e) {
//	e.preventDefault();
//	
//	var parent_account_id = $(this).data('parent-account-id');
//	var direction = $(this).data('direction');
//
//    $('#'+genealogy.req.target_id).trigger('on-add-new-account', [parent_account_id, direction]);
//
//});
//
//$("body").on("click", ".btn-add-new-distributor", function(e) {
//	e.preventDefault();
//	
//	var parent_account_id = $(this).data('parent-account-id');
//	var direction = $(this).data('direction');
//
//    $('#'+genealogy.req.target_id).trigger('on-add-new-distributor', [parent_account_id, direction]);
//
//});
//
//$("body").on("click", ".btn-genealogy-move-up", function(e) {
//	e.preventDefault();
//	
//	genealogy.render({
//		'target_id' : genealogy.req.target_id,
//		'url' : genealogy.req.url,
//		'account_id' : genealogy.account.upline_id
//	});
//});
//
//$("body").on("click", ".btn-genealogy-root", function(e) {
//	e.preventDefault();
//	
//	genealogy.render({
//		'target_id' : genealogy.req.target_id,
//		'url' : genealogy.req.url,
//		'account_id' : genealogy.primary_account.account_id
//	});
//});
//
//// btn-genealogy-search
//
//$("body").on("click", ".btn-genealogy-search", function(e){
//	e.preventDefault();
//	
//	genealogy.search();
//});
//
//$("body").on("click", ".btn-close-genealogy-popover", function(e){
//	genealogy.hover.out_timer = setTimeout(function() {
//		$('#genealogy-popover-info').remove();
//		
//		// clear values
//		genealogy.hover.el = null;
//		genealogy.hover.timer = 0;
//		genealogy.hover.account_id = '';
//		genealogy.hover.member_id = '';
//		genealogy.hover.node = '';
//		
//	},250);
//	
//	clearTimeout(genealogy.hover.in_timer);
//});
//
//$('.g-table-mem-pager')
//
//$("body").on("click", ".g-table-mem-pager .pagination a", function(e) {
//	e.preventDefault();
//	
//	var _href = $(this).attr('href');
//	_href = _href.split("/");
//	var _page = _href[_href.length-1];
//	if (_page == '#') return false;
//	
//	_page = parseInt(_page);
//	if (_.isNaN(_page)) _page = 1;
//	
//	if (_.isNumber(_page)) {
//		genealogy.get_downline(_page);
//	}
//	
//});

// 20131210
// START ==
/*
 * GBS-VitalC Genealogy javascript 
 *
 */


/**
 * jQuery.fn.sortElements
 * --------------
 * @author James Padolsey (http://james.padolsey.com)
 * @version 0.11
 * @updated 18-MAR-2010
 * --------------
 * @param Function comparator:
 *   Exactly the same behaviour as [1,2,3].sort(comparator)
 *   
 * @param Function getSortable
 *   A function that should return the element that is
 *   to be sorted. The comparator will run on the
 *   current collection, but you may want the actual
 *   resulting sort to occur on a parent or another
 *   associated element.
 *   
 *   E.g. $('td').sortElements(comparator, function(){
 *      return this.parentNode; 
 *   })
 *   
 *   The <td>'s parent (<tr>) will be sorted instead
 *   of the <td> itself.
 */
jQuery.fn.sortElements = (function(){

    var sort = [].sort;

    return function(comparator, getSortable) {

        getSortable = getSortable || function(){return this;};

        var placements = this.map(function(){

            var sortElement = getSortable.call(this),
                parentNode = sortElement.parentNode,

                // Since the element itself will change position, we have
                // to have some way of storing it's original position in
                // the DOM. The easiest way is to have a 'flag' node:
                nextSibling = parentNode.insertBefore(
                    document.createTextNode(''),
                    sortElement.nextSibling
                );

            return function() {

                if (parentNode === this) {
                    throw new Error(
                        "You can't sort elements if any one is a descendant of another."
                    );
                }

                // Insert before flag:
                parentNode.insertBefore(this, nextSibling);
                // Remove flag:
                parentNode.removeChild(nextSibling);

            };

        });

        return sort.call(this, comparator).each(function(i){
            placements[i].call(getSortable.call(this));
        });

    };

})();

(function() {
	
	/*
	 * Initialize the genealogy object
	 */
	var root = this;
	
	var genealogy = {};
	
	if (typeof exports !== 'undefined') {
		if (typeof module !== 'undefined' && module.exports) {
			exports = module.exports = genealogy;
		}
		exports.genealogy = genealogy;
		exports.b = genealogy;
	} else {
		root['genealogy'] = genealogy;
		root['g'] = genealogy;
	}
	
	genealogy.req = {};
	genealogy.req.target_id = '';
	genealogy.req.url = '';
	genealogy.req.search_url = '';
	genealogy.req.downline_url = '';
	genealogy.req.downline_handler = null;
	genealogy.req.account_id = '';
	genealogy.ref = {};
	genealogy.ref.members = {};
	genealogy.ref.accounts = {};
	genealogy.account = {};
	genealogy.account_upgrades = {};
	genealogy.is_erhm_mode = {};
	genealogy.primary_account = {};
	genealogy.hover = {};
	genealogy.hover.el = null;
	genealogy.hover.in_timer = 0;
	genealogy.hover.out_timer = 0;
	genealogy.hover.account_id = '';
	genealogy.hover.member_id = '';
	genealogy.templates = {};
	genealogy.templates.popover = "<div id='genealogy-popover-info' class='genealogy-popup'> \
		<div class='genealogy-arrow genealogy-arrow-left'></div> \
 		<div class='genealogy-arrow genealogy-arrow-right'></div> \
		<div class='genealogy-popup-container'> \
			<button class='close btn-close-genealogy-popover'><i class='icon-remove icon-white'></i></button> \
			<%= image_string %> \
			<span class='genealogy-info'><span title='<%= fullname %>' class='genealogy-acct-owner'><%= fullname %></span> </span> \
			<span class='genealogy-info genealogy-padding'><%= account_type_code %> (<%= account_id %>)<span class='insert-timestamp'><%=insert_timestamp%></span></span> \
			<span class='genealogy-info'><abbr title='Upline'>U:</abbr> <span class='genealogy-acct-names'><%= upline_fullname %></span> <span class='account-id'>(<%= upline_id %>)</span></span> \
			<span class='genealogy-info'><abbr title='Sponsor'>S:</abbr> <span class='genealogy-acct-names'><%= sponsor_fullname %></span> <span class='account-id'>(<%= sponsor_id %>)</span></span> \
			<%= upgrade_string %> \
			<%= p2p_product_string %> \
			<div class='genealogy-action'> \
				<button class='btn btn-super-tiny btn-genealogy-network'><i class='icon-search'></i> View Network &nbsp;</button> \
				<button class='btn btn-super-tiny btn-genealogy-message' data-member-account-id='<%= member_account_id %>' data-member-fullname='<%= propername %>'><i class='icon-search'></i> Send PM &nbsp;</button> \
			</div> \
		</div> \
	</div>";
	
	genealogy.templates.popoverUpgrade = "<div id='genealogy-popover-info' class='genealogy-popup'> \
		<div class='genealogy-arrow genealogy-arrow-left'></div> \
 		<div class='genealogy-arrow genealogy-arrow-right'></div> \
		<div class='genealogy-popup-container'> \
			<button class='close btn-close-genealogy-popover'><i class='icon-remove icon-white'></i></button> \
			<%= image_string %> \
			<span class='genealogy-info'><span title='<%= fullname %>'  class='genealogy-acct-owner'><%= fullname %></span> </span> \
			<span class='genealogy-info genealogy-padding'><%= account_type_code %> (<%= account_id %>)<span class='insert-timestamp'><%=insert_timestamp%></span></span> \
			<span class='genealogy-info'><abbr title='Upline'>U:</abbr> <%= upline_fullname %> <span class='account-id'>(<%= upline_id %>)</span></span> \
			<span class='genealogy-info'><abbr title='Sponsor'>S:</abbr> <%= sponsor_fullname %> <span class='account-id'>(<%= sponsor_id %>)</span></span> \
			<%= upgrade_string %> \
			<%= p2p_product_string %> \
			<div class='genealogy-action'> \
				<button class='btn btn-super-tiny btn-genealogy-network'><i class='icon-search'></i> View Network &nbsp;</button> \
				<button class='btn btn-super-tiny btn-genealogy-message' data-member-account-id='<%= member_account_id %>' data-member-fullname='<%= propername %>'><i class='icon-search'></i> Send PM &nbsp;</button> \
				<button class='btn btn-super-tiny btn-genealogy-upgrade' data-member-account-id='<%= member_account_id %>' data-member-fullname='<%= propername %>'><i class='icon-search'></i> Upgrade &nbsp;</button> \
			</div> \
		</div> \
	</div>";

	genealogy.templates.emptyPopover = "<div id='genealogy-popover-info' class='genealogy-popup'> \
		<div class='genealogy-arrow genealogy-arrow-left'></div> \
 		<div class='genealogy-arrow genealogy-arrow-right'></div> \
		<div class='genealogy-popup-container'> \
			<button class='close btn-close-genealogy-popover'><i class='icon-remove icon-white'></i></button> \
			<span class='genealogy-info' style='font-size: 18px;line-height: 50px; text-align:center;'>Available Slot</span> \
			<div class='genealogy-action'> \
				<button class='btn btn-super-tiny btn-add-new-account' data-parent-account-id='<%= parent_account_id %>' data-direction='<%= direction %>' ><i class='icon-plus-sign'></i> Add Account &nbsp;</button> \
				<button class='btn btn-super-tiny btn-add-new-distributor' data-parent-account-id='<%= parent_account_id %>' data-direction='<%= direction %>' ><i class='icon-plus-sign'></i> Add Distributor &nbsp;</button> \
			</div> \
		</div> \
	</div>";

	genealogy.templates.erhmContainer = " \
		<div class='node-erhm-container'> \
		  <div class='node-upgrade-partition node-dol'></div> \
		  <div class='node-upgrade-partition node-p2p'></div> \
		  <div class='node-upgrade-partition node-up1'></div> \
		  <div class='node-upgrade-partition node-bottom'></div> \
		</div> \
	";
	
	genealogy.templates.network_erhm = "  \
		<div class='genealogy-legend-box'> \
			<div class='clearfix'><div class='node-legend-box node-available'></div><span> - Available</span></div> \
			<div class='clearfix'><div class='node-legend-box node-dol-color'></div><span> - DOLLAR</span></div> \
			<div class='clearfix'><div class='node-legend-box node-p2p-color'></div><span> - P2P</span></div> \
			<div class='clearfix'><div class='node-legend-box node-up1-color'></div><span> - UPGRADE1</span></div> \
			<div class='clearfix'><div class='node-legend-box node-erhm-color'></div><span> - ERHM</span></div> \
			<div class='clearfix'><div class='node-legend-box node-cd-color'></div><span> - ERHM-CD</span></div> \
			<div class='clearfix'><div class='node-legend-box node-fs-rhm'></div><span> - ERHM-FS</span></div> \
			<div class='clearfix'><div class='node-legend-box node-maint'>M</div><span> - No Monthly Maint.</span></div> \
			<div class='clearfix'><div class='node-legend-box node-maint'>A</div><span> - No Annual Maint.</span></div> \
		</div> \
		";
		
	genealogy.templates.network_non_erhm = "  \
		<div class='genealogy-legend-box'> \
			<div class='clearfix'><div class='node-legend-box node-sp'></div><span> - SP</span></div> \
			<div class='clearfix'><div class='node-legend-box node-vp'></div><span> - VP</span></div> \
			<div class='clearfix'><div class='node-legend-box node-tp'></div><span> - TP</span></div> \
			<div class='clearfix'><div class='node-legend-box node-cd'></div><span> - CD</span></div> \
			<div class='clearfix'><div class='node-legend-box node-fs'></div><span> - FS</span></div> \
			<div class='clearfix'><div class='node-legend-box node-fs-rhm'></div><span> - ERHM-FS</span></div> \
			<div class='clearfix'><div class='node-legend-box node-maint'>M</div><span> - No Monthly Maint.</span></div> \
			<div class='clearfix'><div class='node-legend-box node-maint'>A</div><span> - No Annual Maint.</span></div> \
		</div> \
		";	
	
	genealogy.templates.network = "  \
		<div class='genealogy-button-box'> \
			<button class='btn btn-success btn-small btn-genealogy-move btn-genealogy-move-up'>Move 1 Level Up</button> \
			<button class='btn btn-success btn-small btn-genealogy-move btn-genealogy-root'>Move to Top</button> \
			<button class='btn btn-info btn-small btn-genealogy-search'><i class='icon-search icon-white'></i>&nbsp;Search</button> \
		</div> \
		<div class='genealogy-level level1'> \
			<div data-node='0' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
		</div> \
		<div class='genealogy-level level2'> \
			<div data-node='2' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='1' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
		</div> \
		<div class='genealogy-level level3'> \
			<div data-node='22' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='21' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='12' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='11' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
		</div> \
		<div class='genealogy-level level4'> \
			<div data-node='222' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='221' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
 			</div> \
			<div data-node='212' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='211' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='122' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='121' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='112' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='111' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
		</div> \
		<div class='genealogy-level level5'> \
			<div data-node='2222' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='2221' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='2212' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='2211' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='2122' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='2121' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='2112' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='2111' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='1222' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='1221' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='1212' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='1211' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='1122' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='1121' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='1112' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
			<div data-node='1111' class='node-box'> \
				<div class='center-line top'></div> \
				<div class='center-line bottom'></div> \
				<div class='bottom-line'></div> \
				<div class='node-content'></div> \
				<div class='node-extra-legend'> \
					<span class='maint-legend maint-monthly'>M</span> \
					<span class='maint-legend maint-annual'>A</span> \
				</div> \
			</div> \
		</div> ";
	
	genealogy.templates.network_list_container = "<div class='g-table-mem-list' > \
		<table class='table table-bordered table-condensed '> \
			<thead> \
				<tr> \
					<th class='g-table-account-id' >Account ID</th> \
					<th >Account Name</th> \
					<th class='g-table-account-id' >Upline</th> \
					<th class='g-table-pos' >Pos</th> \
					<th class='g-table-dt' >Date Registered</th> \
					<th class='g-table-type' >Type</th> \
					<th class='g-table-count' >L</th> \
					<th class='g-table-count' >R</th> \
				</tr> \
			</thead> \
			<tbody class='g-table-mem-items'> \
				<tr><td colspan='8'>Loading... <img src='/assets/img/loading2.gif' alt='' style='height:55%;' /></td></tr> \
			</tbody> \
		</table> \
		<div class='g-table-mem-pager'></div> \
	</div>";
	
	genealogy.templates.network_list_item = " \
			<% \
				$.each(accounts, function(index, item) { \
						var _mem = genealogy.ref.members[item.member_id]; \
						var _fullname = (_mem.first_name + ' ' + _mem.last_name).toUpperCase(); \
						var _type = ''; \
						if (item.account_type_id == 1) \
							_type = 'SP'; \
						else if (item.account_type_id == 2) \
							_type = 'VP'; \
						else if (item.account_type_id == 3) \
							_type = 'TP'; \
						else if (item.account_type_id == 4) \
							_type = 'CD'; \
						else if (item.account_type_id == 5) \
							_type = 'FS'; \
						var _pos = item.side == '2' ? 'L' : 'R'; \
			%> \
				<tr data-node-address='<%= item.node_address %>' data-account-id='<%= item.account_id %>'> \
					<td class='g-table-account-id' ><%= item.account_id %></td> \
					<td ><%= _fullname %></td> \
					<td class='g-table-account-id' ><%= item.upline_id %></td> \
					<td class='g-table-pos' ><%= _pos %></td> \
					<td class='g-table-dt' ><%= item.insert_timestamp %></td> \
					<td class='g-table-type' ><%= item.account_type_code %></td> \
					<td class='g-table-count left-side' ><img src='/assets/img/loading2.gif' alt='' style='width:40%;' /></td> \
					<td class='g-table-count right-side' ><img src='/assets/img/loading2.gif' alt='' style='width:40%;' /></td> \
				</tr> \
			<% \
				}); \
			%> \
	";
	
	genealogy.templates.search_result = "<form id='frm_g_member_search' class='form-search' onsubmit='return false;' > \
	            <input id='g_search_key' name='g_search_key' type='text' class='input-xxlarge search-query' placeholder='Enter Account ID or Name' value='' /> \
	            <button id='btn_g_search' class='btn'><i class='icon-search'></i>&nbsp;Search</button> \
	          </form> \
	<table class='table table-striped table-condensed g-member-list'> \
		<thead> \
			<tr> \
				<th class='account_number'>Account ID</th> \
				<th class='level_away'>Lvl Down</th> \
				<th>Name</th> \
				<th style='width:150px;'>Member Type</th> \
				<th class='action'>&nbsp;</th> \
			</tr> \
		</thead> \
	</table> \
	<div id='g-member-list'> \
		<table class='table table-striped table-condensed g-member-list'> \
			<tbody id='g-member-listing'> \
			</tbody> \
		</table> \
	</div>";
	
	genealogy.templates.search_result_item = "<% $.each(members, function(index, item) { %> \
			<tr id='cust_<%= item.account_id %>'> \
				<td class='account_number'><%= item.account_id %></td> \
				<td class='level_away'><%= item.level_away %></td> \
				<td><%= item.fullname %></td> \
				<td><%= item.member_type %></td> \
				<td class='action'><button class='btn btn-small btn-info btn_g_select_member' data-id='<%= item.account_id %>'>Select</button></td> \
			</tr> \
	<%	}); %>";
		
	genealogy.reset = function() {
		genealogy.req.target_id = '';
		genealogy.req.url = '';
		genealogy.req.search_url = '';
		genealogy.req.downline_url = '';
		genealogy.req.downline_handler = null;
		genealogy.req.account_id = '';
		genealogy.ref.members = {};
		genealogy.ref.accounts = {};
		genealogy.network = {};
	};
	
	genealogy.get = function(options, cb, error_cb) {
		if (typeof(options) == 'undefined') return;
		if (typeof(options.url) == 'undefined') return;
		if (typeof(options.account_id) == 'undefined') return;
		
		genealogy.req.url = options.url;
		genealogy.req.account_id = options.account_id;
		$('#'+genealogy.req.target_id+' .genealogy-container .genealogy-tree-container').html("<div style='margin:10px;'>Loading... <img src='/assets/img/loading2.gif' alt='' /></div>");
		
		// cancel downline count request
		if (_.isObject(genealogy.req.downline_handler))
			if (typeof(genealogy.req.downline_handler.abort) != 'undefined') genealogy.req.downline_handler.abort();
		b.request({
			with_overlay: false,
			url: genealogy.req.url,
			data: {'account_id' : genealogy.req.account_id},
			on_success: function(data, status) {
				if (data.status == 'ok') {
					genealogy.account = data.data.account;
					genealogy.account_upgrades = data.data.account_upgrades;
					genealogy.is_erhm_mode = data.data.is_erhm_mode;
					
					if (_.size(genealogy.primary_account) == 0) {
						genealogy.primary_account = _.clone(genealogy.account);
					}
					
					genealogy.ref.members = data.data.members;
					genealogy.ref.accounts = data.data.accounts;
					if (_.isFunction(cb)) cb.call(this);
				} else {
					if (_.isFunction(error_cb)) error_cb.call(this, data);
				}
			}
		});
	};
	
	genealogy.displayNetwork = function() {
	
		if (typeof(genealogy.account.network) == 'undefined') return;
		$('[data-node="0"]').data('account-id', genealogy.account.account_id);
		$('[data-node="0"]').data('member-id', genealogy.account.member_id);
		$('[data-node="0"]').removeClass('blank');
		
		//alert(genealogy.account_upgrades.erhm_upgrade + genealogy.account_upgrades.up1_upgrade + genealogy.account_upgrades.p2p_upgrade);
		
		//if (genealogy.account.account_type_id == 1)
		//	$('[data-node="0"]').addClass('node-sp');
		//else if (genealogy.account.account_type_id == 2)
		//	$('[data-node="0"]').addClass('node-vp');
		//else if (genealogy.account.account_type_id == 3)
		//	$('[data-node="0"]').addClass('node-tp');
		//else if (genealogy.account.account_type_id == 4)
		//	$('[data-node="0"]').addClass('node-cd');
		//else if (genealogy.account.account_type_id == 5)
		//	$('[data-node="0"]').addClass('node-fs')
		//else if (genealogy.account.account_type_id == 8)
		//	$('[data-node="0"]').addClass('node-fs-rhm');
		
		//if (genealogy.account_upgrades.erhm_upgrade == 1)
		//$('[data-node="0"]').find('.node-erhm').css('opacity','1');	
		$('[data-node="0"]').find('.node-bottom').addClass('node-'+genealogy.account.account_type_code.toLowerCase()).css('opacity','1');	
		
		if (genealogy.is_erhm_mode == 0) {
			if (genealogy.account_upgrades.account_type_code == 'SP') {
				$('[data-node="0"]').find('.node-erhm').css('background','#69E82E');
			} else if (genealogy.account_upgrades.account_type_code == 'VP') {
				$('[data-node="0"]').find('.node-erhm').css('background','#307EFC');
			} else if (genealogy.account_upgrades.account_type_code == 'TP') {
				$('[data-node="0"]').find('.node-erhm').css('background','#A3FA7A');
			} else if (genealogy.account_upgrades.account_type_code == 'CD') {
				$('[data-node="0"]').find('.node-erhm').css('background','#F74219');
			} else if (genealogy.account_upgrades.account_type_code == 'FS') {
				$('[data-node="0"]').find('.node-erhm').css('background','#FF6501');
			}  else if (genealogy.account_upgrades.account_type_code == 'FS-RHM') {
				$('[data-node="0"]').find('.node-erhm').css('background','#F89406');
			}
		} else {
			if (genealogy.account_upgrades.account_type_code == 'FS') {
				$('[data-node="0"]').find('.node-erhm').css('background','#F89406');
			} else if (genealogy.account_upgrades.account_type_code == 'CD-ERHM') {
				$('[data-node="0"]').find('.node-erhm').css('background','#F74219');
			}
		}
		
		if (genealogy.account_upgrades.up1_upgrade == 1)
			$('[data-node="0"]').find('.node-up1').css('opacity','1');	
		if (genealogy.account_upgrades.p2p_upgrade == 1)
			$('[data-node="0"]').find('.node-p2p').css('opacity','1');	;	
			
		//$('[data-node="0"] .node-content').html("<img src='/media/profile/"+genealogy.account.member_id+".jpg?v=" + (new Date().getTime()) + "' alt='' />");

		// show maint legends
		if ((typeof(genealogy.account.ms_monthly_maintenance_ctr) != 'undefined') && (typeof(genealogy.account.monthly_maintenance_ctr) != 'undefined')) 		
			if ((parseInt(genealogy.account.ms_monthly_maintenance_ctr) + parseInt(genealogy.account.monthly_maintenance_ctr)) < 2) {
				$('[data-node="0"] .node-extra-legend .maint-monthly').show();
			}

		if ((typeof(genealogy.account.ms_annual_maintenance_ctr) != 'undefined') && (typeof(genealogy.account.annual_maintenance_ctr) != 'undefined'))		
			if ((parseInt(genealogy.account.ms_annual_maintenance_ctr) + parseInt(genealogy.account.annual_maintenance_ctr)) < 4) {
				$('[data-node="0"] .node-extra-legend .maint-annual').show();
			}
				
		$.each(genealogy.account.network, function(index, item) {
			$('[data-node="'+item.node_address+'"]').data('account-id', item.account_id);
			$('[data-node="'+item.node_address+'"]').data('member_id-id', item.member_id);
			$('[data-node="'+item.node_address+'"]').removeClass('blank');
			
			//if (item.erhm_upgrade == 1) {
			//	//$('[data-node="'+item.node_address+'"]').addClass('node-sp');
			//	//console.log($('[data-node="'+item.node_address+'"]').find('.node-up1').length);
			//	$('[data-node="'+item.node_address+'"]').find('.node-erhm').css('opacity','1');
			//}
			//else if (item.account_type_id == 2)
			//	$('[data-node="'+item.node_address+'"]').addClass('node-vp');
			//else if (item.account_type_id == 3)
			//	$('[data-node="'+item.node_address+'"]').addClass('node-tp');
			//else if (item.account_type_id == 4)
			//	$('[data-node="'+item.node_address+'"]').addClass('node-cd');
			//else if (item.account_type_id == 5)
			//	$('[data-node="'+item.node_address+'"]').addClass('node-fs');
			//else if (item.account_type_id == 8)
			//	$('[data-node="'+item.node_address+'"]').addClass('node-fs-rhm');	
		
			//if (item.erhm_upgrade == 1)
				//console.log($('[data-node="'+item.node_address+'"]').find('.node-up1').length);
			//$('[data-node="'+item.node_address+'"]').find('.node-erhm').css('opacity','1');
			$('[data-node="'+item.node_address+'"]').find('.node-bottom').addClass('node-'+item.account_type_code.toLowerCase()).css('opacity','1');
			
			if (genealogy.is_erhm_mode == 0) {	
				if (item.account_type_code == 'SP') {
					$('[data-node="'+item.node_address+'"]').find('.node-erhm').css('background','#69E82E');
				} else if (item.account_type_code == 'VP') {
					$('[data-node="'+item.node_address+'"]').find('.node-erhm').css('background','#307EFC');
				} else if (item.account_type_code == 'TP') {
					$('[data-node="'+item.node_address+'"]').find('.node-erhm').css('background','#A3FA7A');
				} else if (item.account_type_code == 'CD') {
					$('[data-node="'+item.node_address+'"]').find('.node-erhm').css('background','#F74219');
				} else if (item.account_type_code == 'FS') {
					$('[data-node="'+item.node_address+'"]').find('.node-erhm').css('background','#FF6501');
				}  else if (item.account_type_code == 'FS-RHM') {
					$('[data-node="'+item.node_address+'"]').find('.node-erhm').css('background','#F89406');
				}
			} else {
				if (genealogy.account_upgrades.account_type_code == 'FS') {
				$('[data-node="'+item.node_address+'"]').find('.node-erhm').css('background','#FF6501');
			} else if (genealogy.account_upgrades.account_type_code == 'CD-ERHM') {
				$('[data-node="'+item.node_address+'"]').find('.node-erhm').css('background','#F74219');
			}
			}
			
			
			// upgrades
			if (item.up1_upgrade == 1)
				$('[data-node="'+item.node_address+'"]').find('.node-up1').css('opacity','1');
			if (item.p2p_upgrade == 1)
				$('[data-node="'+item.node_address+'"]').find('.node-p2p').css('opacity','1');		

			//$('[data-node="'+item.node_address+'"] .node-content').html("<img src='/media/profile/"+item.member_id+".jpg?v=" + (new Date().getTime()) + "' alt='' />");

			//alert(item.ms_monthly_maintenance_ctr + '|' + item.monthly_maintenance_ctr);
			
			// show maint legends
			if ((typeof(item.ms_monthly_maintenance_ctr) != 'undefined') && (typeof(item.monthly_maintenance_ctr) != 'undefined'))
			
				if ((parseInt(item.ms_monthly_maintenance_ctr) + parseInt(item.monthly_maintenance_ctr)) < 2) {
					$('[data-node="'+item.node_address+'"] .node-extra-legend .maint-monthly').show();
				}

			if ((typeof(item.ms_annual_maintenance_ctr) != 'undefined') && (typeof(item.annual_maintenance_ctr) != 'undefined'))
				if ((parseInt(item.ms_annual_maintenance_ctr) + parseInt(item.annual_maintenance_ctr)) < 4) {
					$('[data-node="'+item.node_address+'"] .node-extra-legend .maint-annual').show();
				}
		});
		
		var _node_address = [];
		$.each($('.node-box.blank'), function(index, item) {
			var node_address = $(item).data('node');
			node_address = String(node_address);
			var parent_node_address = node_address.substring(0, node_address.length-1);
			if (parent_node_address.length == 0) parent_node_address = '0';

			if ($('[data-node="'+parent_node_address+'"]').hasClass('blank') == false) {
				_node_address.push(node_address);
			}
		});
		
		$.each(_node_address, function(index, item) {
			$('[data-node="'+item+'"]').removeClass('blank');
			$('[data-node="'+item+'"]').addClass('node-available');
		});
	};
	
	genealogy.renderPopover = function(el, account_id ) {
		
		if (typeof(genealogy.ref.accounts[account_id]) == 'undefined') return;
		
		var _account = genealogy.ref.accounts[account_id];
		var _upline = genealogy.ref.accounts[_account.upline_id];
		var _sponsor = genealogy.ref.accounts[_account.sponsor_id];
		var upgrade_string = '';
		var up1_string = '';
		var p2p_string = '';
		var p2p_product_string = '';
		var image_string = '';
		
		var with_upgrade = 0;		
		if ((!_account.up1_upgrade[0]) || (!_account.p2p_upgrade[0]))
			with_upgrade = 1;
		
		if(_account.up1_upgrade[0] || _account.p2p_upgrade[0] )
		{
			if(_account.up1_upgrade[0])
			{
				up1_string = "<span class='genealogy-info genealogy-upgrade-container'>UP1 ("+_account.up1_upgrade[0].upgrade_account_id+")<span class='insert-timestamp'>"+_account.up1_upgrade[0].insert_timestamp+"</span></span> ";
			}
			if(_account.p2p_upgrade[0])
			{
				if(!_account.up1_upgrade[0])
					p2p_string = "<span class='genealogy-info genealogy-upgrade-container'>(P-P)<sup>3</sup> ("+_account.p2p_upgrade[0].upgrade_account_id+")<span class='insert-timestamp'>"+_account.p2p_upgrade[0].insert_timestamp+"</span></span> ";
				else
					p2p_string = "<span class='genealogy-info'>(P-P)<sup>3</sup> ("+_account.p2p_upgrade[0].upgrade_account_id+")<span class='insert-timestamp'>"+_account.p2p_upgrade[0].insert_timestamp+"</span></span> ";
			}
			upgrade_string += up1_string + p2p_string + '</span>';
		}

		if(_account.p2p_products[0])
		{
			p2p_product_string += "<span class='genealogy-info genealogy-upgrade-container'><span class='genealogy-product-name'>"+_account.p2p_products[0].product_name+"</span><span class='product-qty'>x"+_account.p2p_products[0].qty+"</span></span> ";
			if(_account.p2p_products[1])
				p2p_product_string += "<span class='genealogy-info'><span class='genealogy-product-name'>"+_account.p2p_products[1].product_name+"</span><span class='product-qty'>x"+_account.p2p_products[1].qty+"</span></span> ";
			//p2p_product_string += "</span>";
		}

		var data = {
			'fullname' : String(genealogy.ref.members[_account.member_id].first_name + ' ' + genealogy.ref.members[_account.member_id].last_name).toUpperCase(),
			'account_id' : account_id,
			'upline_fullname' : String(genealogy.ref.members[_upline.member_id].first_name + ' ' + genealogy.ref.members[_upline.member_id].last_name).toUpperCase(),
			'upline_id' : _account.upline_id,
			'sponsor_fullname' : String(genealogy.ref.members[_sponsor.member_id].first_name + ' ' + genealogy.ref.members[_sponsor.member_id].last_name).toUpperCase(),
			'sponsor_id' : _account.sponsor_id,
            'member_account_id' : account_id,
            'propername' : String(genealogy.ref.members[_account.member_id].first_name + ' ' + genealogy.ref.members[_account.member_id].last_name).toUpperCase(),
			'with_upgrade' : with_upgrade,
			'account_type_code' : genealogy.ref.accounts[account_id].account_type_code,
			'insert_timestamp' : genealogy.ref.accounts[account_id].insert_timestamp,
			'upgrade_string': upgrade_string,
			'p2p_product_string': p2p_product_string,
			'image_string':  genealogy.ref.accounts[account_id].image_filename
		};
		
		var html = '';
		if (with_upgrade == 0) { 
			html = _.template(genealogy.templates.popover, data);
		} else {
			html = _.template(genealogy.templates.popoverUpgrade, data);
		}
		
		// remove existing popover
		$('#genealogy-popover-info').remove();
		var $popover = $(html);
		
		var _pos = $(el).position();
		var _l_m = $(el).css('margin-left');
		var _p_l_m = $(el).parent().css('margin-left');
		var _p_pos  = $(el).parent().position();
		var _w = $(el).outerWidth();
		var _offset = 2;
		
		_l_m = parseInt(String(_l_m).replace('px', ''));
		_p_l_m = parseInt(String(_p_l_m).replace('px', ''));
		
		var _t = _pos.top + _p_pos.top;
		var _l = _pos.left + _p_pos.left + _l_m + _p_l_m + _offset;
		var _total_w = 300;
		
		var _arrow = 'l';			
		if (_l+_total_w > 700) {
			_l = _l - (302  + _offset);
			_arrow = 'r';
		}
		 
		$popover.css({'top' : _t, 'left' : _l});
		
		// put it in
		$('.genealogy-container').append($popover);
		
		$popover.find('.genealogy-arrow').hide();
		if (_arrow == 'l') {
			$popover.find('.genealogy-popup-container').addClass('left');
			$popover.find('.genealogy-arrow-left').show();
		} else {
			$popover.find('.genealogy-arrow-right').show();
		}
			
		$popover.show();
		
		if (el.data('node') == '0')
			$('.btn-genealogy-network').hide();
		
		if (account_id == genealogy.primary_account.account_id)
			$('.btn-genealogy-message').hide();
		
	};
	
	genealogy.renderEmptyPopover = function(el, parent_account_id, direction ) {
		
		var html = _.template(genealogy.templates.emptyPopover, {'parent_account_id' : parent_account_id, 'direction' : direction});
		
		// remove existing popover
		$('#genealogy-popover-info').remove();
		var $popover = $(html);
		
		var _pos = $(el).position();
		var _l_m = $(el).css('margin-left');
		var _p_l_m = $(el).parent().css('margin-left');
		var _p_pos  = $(el).parent().position();
		var _w = $(el).outerWidth();
		var _offset = 2;
		
		_l_m = parseInt(String(_l_m).replace('px', ''));
		_p_l_m = parseInt(String(_p_l_m).replace('px', ''));
		
		var _t = _pos.top + _p_pos.top;
		var _l = _pos.left + _p_pos.left + _l_m + _p_l_m + _offset;
		var _total_w = 280;
		
		var _arrow = 'l';			
		if (_l+_total_w > 700) {
			_l = _l - (282  + _offset);
			_arrow = 'r';
		}
		 
		$popover.css({'top' : _t, 'left' : _l});
		
		// put it in
		$('.genealogy-container').append($popover);
		
		$popover.find('.genealogy-arrow').hide();
		if (_arrow == 'l') {
			$popover.find('.genealogy-popup-container').addClass('left');
			$popover.find('.genealogy-arrow-left').show();
		} else {
			$popover.find('.genealogy-arrow-right').show();
		}
			
		$popover.show();
		
	};
	
	genealogy.render = function(options) {
		
		$('#genealogy-popover-info').remove();
		
		if (typeof(options) == 'undefined') return;
		if (typeof(options.target_id) == 'undefined') return;
		if (typeof(options.url) == 'undefined') return;
		if (typeof(options.account_id) == 'undefined') return;
		
		genealogy.req.target_id = options.target_id;
		
		if (typeof(options.search_url) != 'undefined')
			genealogy.req.search_url = options.search_url;
			
		if (typeof(options.downline_url) != 'undefined')
			genealogy.req.downline_url = options.downline_url;
			
		if ($('#'+genealogy.req.target_id+' .genealogy-container').length == 0)
			$('#'+genealogy.req.target_id).html("<div class='genealogy-container'><div class='genealogy-tree-container'></div>"+genealogy.templates.network_list_container+"</div>");
		
		genealogy.get({
			'url' : options.url, 
			'account_id' : options.account_id
		}, function() {
			
			var html =  ""
			
			if (genealogy.is_erhm_mode == 0) {
				html = _.template(genealogy.templates.network_non_erhm + genealogy.templates.network, {});
			} else {
				html = _.template(genealogy.templates.network_erhm + genealogy.templates.network, {});
			}
			var $network = $(html);
			$network.find('.node-box').each(function(){ $(this).prepend(_.template(genealogy.templates.erhmContainer, {})); });
			$network.find('.node-box').addClass('blank');
			if (genealogy.primary_account.account_id != genealogy.account.account_id) {
				$network.find('.btn-genealogy-move').show();
			}
			$('#'+options.target_id+' .genealogy-container .genealogy-tree-container').html($network);
			genealogy.displayNetwork();

			genealogy.get_downline();
			
			if (_.isFunction(options.on_success)) options.on_success.call(this);
		}, options.on_error);
		
	};
	
	
	genealogy.get_downline = function(page) {
		
		page = typeof(page) == 'undefined' ? 1 : page;
		
		$('.g-table-mem-items').html("<tr><td colspan='8'>Loading... <img src='/assets/img/loading2.gif' alt='' style='height:55%;' /></td></tr>");
		
		// get donwline 
		genealogy.req.downline_handler = b.request({
			with_overlay: false,
			url: genealogy.req.downline_url + '/' + page,
			data: {'account_id' : genealogy.account.account_id},
			on_success: function(data, status) {
				if (data.status == 'ok') {
					var _accounts = data.data.accounts;
					var _members = data.data.members;
					
					$.each(_members, function(index, item) {
						if (typeof(genealogy.ref.members[index]) == 'undefined')
							genealogy.ref.members[index] = _.clone(item);
					});
					
					var _account_ids = [];
					$.each(_accounts, function(index, item) {
						_account_ids.push(item.account_id);
					});
					
					$('.g-table-mem-items').html(_.template(genealogy.templates.network_list_item, {'accounts' : _accounts}));
					$('.g-table-mem-pager').html(data.data.pager);
					
					// get downline counts
					genealogy.req.downline_handler = b.request({
						with_overlay: false,
						url: genealogy.req.downline_url+'_count',
						data: {'account_ids' : _account_ids},
						on_success: function(data, status) {
							var _accounts = data.data;
							$.each(_accounts, function(index, item) {
								$("tr[data-account-id='"+index+"'] td.left-side").html(item.left);
								$("tr[data-account-id='"+index+"'] td.right-side").html(item.right);
							});
						}
					});
					
				}
			}
		});
		
	};
	
	genealogy.search = function() {
		
		var modal = b.modal.create({
			title: "Member Search",
			html: _.template(genealogy.templates.search_result, {}),
			width: 700
		});
		modal.show();
		
		$('#btn_g_search').click(function(e) {
			e.preventDefault();
			
			var _search_key = $.trim($('#g_search_key').val());
			
			if (_search_key.length == 0 ) return false;
			
			b.request({
				url: genealogy.req.search_url,
				data: {'root_account_id' : genealogy.primary_account.account_id, 'search_key' : _search_key},
				on_success: function(data, status) {

					if (data.status == 'ok') {
						var member_accounts = data.data;
						$('#g-member-listing').html(_.template(genealogy.templates.search_result_item, {'members' : member_accounts}));
						$('#g-member-listing td:nth-child(3)').highlight(_search_key);
					} else {
						$('#g-member-listing').html("<td>"+data.msg+"</td>");
					}
				}
			});
			
		});
		
		$("body").undelegate(".btn_g_select_member", "click");
		$("body").delegate(".btn_g_select_member", "click", function(e) {
			e.preventDefault();
			
			var _acct_id = $.trim($(this).data('id'));
			if (_acct_id.length == 0) return false;
			genealogy.render({
				'target_id' : genealogy.req.target_id,
				'url' : genealogy.req.url,
				'account_id' : _acct_id
			});
			
			modal.hide();
			
		});
		
		
		
	};
	
	
}).call(this);

$("body").on("click", ".node-content", function(e) {
	var $el = $(this).parent();
	clearTimeout(genealogy.hover.in_timer);
	clearTimeout(genealogy.hover.out_timer);
	genealogy.hover.in_timer = setTimeout(function() {
		genealogy.hover.el = $el;
		genealogy.hover.account_id = genealogy.hover.el.data('account-id');
		genealogy.hover.member_id = genealogy.hover.el.data('member-id');
		genealogy.hover.node = genealogy.hover.el.data('node');
		
		if (typeof(genealogy.hover.account_id) == 'undefined') genealogy.hover.account_id = '';
		if (typeof(genealogy.hover.member_id) == 'undefined') genealogy.hover.member_id = '';
		if (genealogy.hover.account_id != '') {
			genealogy.renderPopover(genealogy.hover.el, genealogy.hover.account_id);
		} else {
			if (genealogy.hover.el.hasClass('node-available')) {
				var parent_account_id = 0;
				var node_address = String(genealogy.hover.el.data('node'));
				var direction = node_address[node_address.length-1];
				if (node_address.length == 1) {
					parent_account_id = $('[data-node="0"]').first().data('account-id');
				} else {
					parent_account_id = $('[data-node="'+node_address.substring(0, node_address.length-1)+'"]').first().data('account-id');
				}
				genealogy.renderEmptyPopover(genealogy.hover.el, parent_account_id, direction);
			}
		}
		
	},150);

});


$("body").on("mouseleave", ".node-box", function(e){

	if ($('#genealogy-popover-info').length == 0) {

		clearTimeout(genealogy.hover.in_timer);
		
		// clear values
		genealogy.hover.el = null;
		genealogy.hover.timer = 0;
		genealogy.hover.account_id = '';
		genealogy.hover.member_id = '';
		genealogy.hover.node = '';
	}


});

$("body").on("mouseleave", ".genealogy-popup", function(e){

	genealogy.hover.out_timer = setTimeout(function() {
		$('#genealogy-popover-info').remove();
		
		// clear values
		genealogy.hover.el = null;
		genealogy.hover.timer = 0;
		genealogy.hover.account_id = '';
		genealogy.hover.member_id = '';
		genealogy.hover.node = '';
		
	},250);
	
	clearTimeout(genealogy.hover.in_timer);

});

$("body").on("click", ".btn-genealogy-network", function(e) {
	e.preventDefault();
	
	if (genealogy.hover.account_id.length > 0) {
		genealogy.render({
			'target_id' : genealogy.req.target_id,
			'url' : genealogy.req.url,
			'account_id' : genealogy.hover.account_id
		});
	}
});

$("body").on("click", ".btn-genealogy-message", function(e) {
	e.preventDefault();
	
    var member_account_id = $(this).data('member-account-id');
    var member_fullname = $(this).data('member-fullname');
    $('#'+genealogy.req.target_id).trigger('on-send-private-message', [member_account_id, member_fullname]);

});

$("body").on("click", ".btn-genealogy-upgrade", function(e) {
	e.preventDefault();
	
    var member_account_id = $(this).data('member-account-id');
    var member_fullname = $(this).data('member-fullname');
    $('#'+genealogy.req.target_id).trigger('on-upgrade-account', [member_account_id, member_fullname]);

});

$("body").on("click", ".btn-add-new-account", function(e) {
	e.preventDefault();
	
	var parent_account_id = $(this).data('parent-account-id');
	var direction = $(this).data('direction');

    $('#'+genealogy.req.target_id).trigger('on-add-new-account', [parent_account_id, direction]);

});

$("body").on("click", ".btn-add-new-distributor", function(e) {
	e.preventDefault();
	
	var parent_account_id = $(this).data('parent-account-id');
	var direction = $(this).data('direction');

    $('#'+genealogy.req.target_id).trigger('on-add-new-distributor', [parent_account_id, direction]);

});

$("body").on("click", ".btn-genealogy-move-up", function(e) {
	e.preventDefault();
	
	genealogy.render({
		'target_id' : genealogy.req.target_id,
		'url' : genealogy.req.url,
		'account_id' : genealogy.account.upline_id
	});
});

$("body").on("click", ".btn-genealogy-root", function(e) {
	e.preventDefault();
	
	genealogy.render({
		'target_id' : genealogy.req.target_id,
		'url' : genealogy.req.url,
		'account_id' : genealogy.primary_account.account_id
	});
});

// btn-genealogy-search

$("body").on("click", ".btn-genealogy-search", function(e){
	e.preventDefault();
	
	genealogy.search();
});

$("body").on("click", ".btn-close-genealogy-popover", function(e){
	genealogy.hover.out_timer = setTimeout(function() {
		$('#genealogy-popover-info').remove();
		
		// clear values
		genealogy.hover.el = null;
		genealogy.hover.timer = 0;
		genealogy.hover.account_id = '';
		genealogy.hover.member_id = '';
		genealogy.hover.node = '';
		
	},250);
	
	clearTimeout(genealogy.hover.in_timer);
});

$('.g-table-mem-pager')

$("body").on("click", ".g-table-mem-pager .pagination a", function(e) {
	e.preventDefault();
	
	var _href = $(this).attr('href');
	_href = _href.split("/");
	var _page = _href[_href.length-1];
	if (_page == '#') return false;
	
	_page = parseInt(_page);
	if (_.isNaN(_page)) _page = 1;
	
	if (_.isNumber(_page)) {
		genealogy.get_downline(_page);
	}
	
});
// END
// ===





