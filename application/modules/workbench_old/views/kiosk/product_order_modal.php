<a href="#myModal" role="button" class="btn btn-inverse" data-toggle="modal">Launch variants modal</a>
 
<!-- Modal -->
<div id="myModal" class="modal hide fade prod-variants" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Package's Product Variants</h3>
	</div>
	<div class="modal-body">

		<div class="alert alert-success pull-right">
		  	<strong>Group complete of 5 slots</strong> feedback if user fills up the slots. Also indicate how much are currently available out of total slots.
		</div>
		<div class="media pull-left">
			<span class="pull-left prod-thumb"><img src="http://placehold.it/100x100" /></span>
			<div class="media-body">
			    <h4 class="media-heading">Product Package is Not Just Any Package Unless It is Proven Otherwise</h4>
				<p>Standard Retail Price: <strong>P6,944.00</strong><br />
					Member Price: <strong>P6,944.00</strong><br />
					Employee Price: <strong>P6,944.00</strong><br />
					Quantity: <strong>1</strong>
				</p>
			</div>
		</div>
		
		<div class="package-options">
			<div class="groups">
				<div class="heading">
					<h5>Groups</h5>
					<span class='muted'>Click to load products</span>
				</div>
				<ol>
					<li class='active'><a href="#">Group 1 <span class='muted'>(Available: 5)</span></a></li>
					<li><a href="#">Group 2 <span class='muted'>(Available: 5)</span></a></li>
					<li><a href="#">Group 3 <span class='muted'>(Available: 5)</span></a></li>
					<li><a href="#">Group 4 <span class='muted'>(Available: 5)</span></a></li>
					<li><a href="#">Group 5 <span class='muted'>(Available: 5)</span></a></li>
					<li><a href="#">Group 6 <span class='muted'>(Available: 5)</span></a></li>
					<li><a href="#">Group 7 <span class='muted'>(Available: 5)</span></a></li>
				</ol>
				<p class="options-scroll btn-group">
					<button class="btn list-up" type="button"><i class="icon-chevron-up"></i></button>
					<button class="btn list-down" type="button"><i class="icon-chevron-down"></i></button>
				</p>
			</div>
			<div class="products">
				<div class="heading">
					<h5>Products</h5>
					<span class='muted'>Click to add products</span>
				</div>
				<ol>
					<li><a href="#">Product Package is Not Just</a></li>
					<li><a href="#">Product One Item</a></li>
					<li><a href="#">Just One Product</a></li>
					<li><a href="#">So This is an Actual Product?</a></li>
					<li><a href="#">Coconuts</a></li>
					<li><a href="#">Coconuts</a></li>
					<li><a href="#">Coconuts</a></li>
				</ol>
				<p class="options-scroll btn-group">
					<button class="btn list-up" type="button"><i class="icon-chevron-up"></i></button>
					<button class="btn list-down" type="button"><i class="icon-chevron-down"></i></button>
				</p>
			</div>
			<div class="selected">
				<div class="heading">
					<h5>Selected Products</h5>
					<span class='muted'>Click to remove products</span>
				</div>
				<ol class='selected-list'>
					<li>Product One Item <span class='muted'>x1</span></li>
					<li>So This is an Actual Product? <span class='muted'>x1</span></li>
					<li>Coconuts <span class='muted'>x1</span></li>
				</ol>
				<ol class='toggle-list'>
					<li><a href="#">
						<button type="button" class="close">×</button>
						Product One Item</a></li>
					<li><a href="#">
						<button type="button" class="close">×</button>
						So This is an Actual Product?</a></li>
					<li><a href="#">
						<button type="button" class="close">×</button>
						Coconuts x3</a></li>
				</ol>
				<p class="options-scroll btn-group">
					<button class="btn list-up" type="button"><i class="icon-chevron-up"></i></button>
					<button class="btn list-down" type="button"><i class="icon-chevron-down"></i></button>
				</p>
			</div>
		</div>
			
	</div>
	<div class="modal-footer">
		<span class="pull-left"><button class="btn btn-danger">Reset</button></span>

		<span class="pull-right">
			<button class="btn btn-primary">Done</button>
			<button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
		</span>
	</div>
</div>

<div id="orders">
	<div class="orders-tab">
		<h3>Orders</h3>
		<div class="orders-list">			
			<ul>
				<li>
					<strong>Product Name...</strong>
					<small>x3 P50.00</small>
				</li>
				<li>
					<strong>Product Name...</strong>
					<small>x3 P50.00</small>
				</li>
				<li>
					<strong>Product Name...</strong>
					<small>x3 P50.00</small>
				</li>
				<li>
					<strong>Product Name...</strong>
					<small>x3 P50.00</small>
				</li>
				<li>
					<strong>Product Name...</strong>
					<small>x3 P50.00</small>
				</li>
				<li>
					<strong>Product Name...</strong>
					<small>x3 P50.00</small>
				</li>
			</ul>
			<p class="orders-scroll btn-group">
				<button class="btn list-up" type="button"><i class="icon-chevron-up"></i></button>
				<button class="btn list-down" type="button"><i class="icon-chevron-down"></i></button>
			</p>
		</div>
		<div class="order-total">
			<strong>Total</strong>
			<span>P12,500</span>
		</div>
		<button class="btn btn-primary print-trans"><i class="icon-print icon-white"></i> Print Transaction</button>			
	</div>
</div>
