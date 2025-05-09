jQuery(document).ready(function(e) {
	
	function removeActiveClassItem() {
		jQuery('.wpem-admin-addon-category-item-active').removeClass('wpem-admin-addon-category-item-active');
	}

	jQuery('.wpem-feature').on('click', function(){
		removeActiveClassItem();
		jQuery(this).parent().addClass('wpem-admin-addon-category-item-active');
        jQuery('.product_cat-feature-add-ons').show();
		jQuery('.product_cat-virtual-add-ons').hide();
		jQuery('.product_cat-marketing-add-ons').hide();
		jQuery('.product_cat-ticket-selling-add-ons').hide();
    });

	jQuery('.wpem-ticket-selling').on('click', function(){
		removeActiveClassItem();
		jQuery(this).parent().addClass('wpem-admin-addon-category-item-active');
        jQuery('.product_cat-feature-add-ons').hide();
		jQuery('.product_cat-virtual-add-ons').hide();
		jQuery('.product_cat-marketing-add-ons').hide();
		jQuery('.product_cat-ticket-selling-add-ons').show();
    });

	jQuery('.wpem-marketing').on('click', function(){
		removeActiveClassItem();
		jQuery(this).parent().addClass('wpem-admin-addon-category-item-active');
        jQuery('.product_cat-feature-add-ons').hide();
		jQuery('.product_cat-virtual-add-ons').hide();
		jQuery('.product_cat-marketing-add-ons').show();
		jQuery('.product_cat-ticket-selling-add-ons').hide();
    });

	jQuery('.wpem-virtual').on('click', function(){
		removeActiveClassItem();
		jQuery(this).parent().addClass('wpem-admin-addon-category-item-active');
        jQuery('.product_cat-feature-add-ons').hide();
		jQuery('.product_cat-virtual-add-ons').show();
		jQuery('.product_cat-marketing-add-ons').hide();
		jQuery('.product_cat-ticket-selling-add-ons').hide();
    });

	jQuery('.wpem-feature').trigger('click');

    function removeActiveClass() {
		jQuery('.wpem-admin-tab-active').removeClass('wpem-admin-tab-active');
	}

	jQuery('.wpem-extensions-btn').on('click', function(){
		removeActiveClass();
		jQuery(this).parent().addClass('wpem-admin-tab-active');
        jQuery('#wpem-extensions').show();
		jQuery('#wpem-themes').hide();
		jQuery('#wpem-bundle-save').hide();
    });

	jQuery('.wpem-themes-btn').on('click', function(){
		removeActiveClass();
		jQuery(this).parent().addClass('wpem-admin-tab-active');
        jQuery('#wpem-extensions').hide();
		jQuery('#wpem-themes').show();
		jQuery('#wpem-bundle-save').hide();
    });

	jQuery('.wpem-bundle-save-btn').on('click', function(){
		removeActiveClass();
		jQuery(this).parent().addClass('wpem-admin-tab-active');
        jQuery('#wpem-extensions').hide();
		jQuery('#wpem-themes').hide();
		jQuery('#wpem-bundle-save').show();
    });

	jQuery('.wpem-extensions-btn').trigger('click');

	function removeActiveClassCategory() {
		jQuery('.wpem-admin-addon-category-item-active').removeClass('wpem-admin-addon-category-item-active');
	}

	jQuery('.wpem-all').on('click', function(){
		removeActiveClassCategory();
		jQuery(this).parent().addClass('wpem-admin-addon-category-item-active');
        jQuery('.html').show();
		jQuery('.elementor').show();
    });

	jQuery('.wpem-html').on('click', function(){
		removeActiveClassCategory();
		jQuery(this).parent().addClass('wpem-admin-addon-category-item-active');
        jQuery('.html').show();
		jQuery('.elementor').hide();
    });

	jQuery('.wpem-elementor').on('click', function(){
		removeActiveClassCategory();
		jQuery(this).parent().addClass('wpem-admin-addon-category-item-active');
        jQuery('.html').hide();
		jQuery('.elementor').show();
    });

	jQuery('.wpem-all').trigger('click');

});