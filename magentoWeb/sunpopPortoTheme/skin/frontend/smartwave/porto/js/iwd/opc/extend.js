;
//DUMMY FOR EE CHECKOUT
var checkout =  {
		steps : new Array("login", "billing", "shipping", "shipping_method", "payment", "review"),
		
		gotoSection: function(section){
			IWD.OPC.backToOpc();
		},
		accordion:{
			
		}
};


IWD.OPC.prepareExtendPaymentForm =  function(){
	jQuery('.opc-col-left').hide();
	jQuery('.opc-col-center').hide();
	jQuery('.opc-col-right').hide();
	jQuery('.opc-menu p.left').hide();	
	jQuery('#checkout-review-table-wrapper').hide();
	jQuery('#checkout-review-submit').hide();
	
	jQuery('.review-menu-block').addClass('payment-form-full-page');
	
};

IWD.OPC.backToOpc =  function(){
	jQuery('.opc-col-left').show();
	jQuery('.opc-col-center').show();
	jQuery('.opc-col-right').show();
	jQuery('#checkout-review-table-wrapper').show();
	jQuery('#checkout-review-submit').show();
	
	
	
	//hide payments form
	jQuery('#payflow-advanced-iframe').hide();
	jQuery('#payflow-link-iframe').hide();
	jQuery('#hss-iframe').hide();

	
	jQuery('.review-menu-block').removeClass('payment-form-full-page');
	
	IWD.OPC.saveOrderStatus = false;
	
};



IWD.OPC.Plugin = {
		
		observer: {},
		
		
		dispatch: function(event, data){
				
			
			if (typeof(IWD.OPC.Plugin.observer[event]) !="undefined"){
				
				var callback = IWD.OPC.Plugin.observer[event];
				callback(data);
				
			}
		},
		
		event: function(eventName, callback){
			IWD.OPC.Plugin.observer[eventName] = callback;
		}
};

/** 3D Secure Credit Card Validation - CENTINEL **/
IWD.OPC.Centinel = {
	init: function(){
		IWD.OPC.Plugin.event('savePaymentAfter', IWD.OPC.Centinel.validate);
	},
	
	validate: function(){
		var c_el = jQuery('#centinel_authenticate_block');
		if(typeof(c_el) != 'undefined' && c_el != undefined && c_el){
			if(c_el.attr('id') == 'centinel_authenticate_block'){
				IWD.OPC.prepareExtendPaymentForm();
			}
		}
	},
	
	success: function(){
		var exist_el = false;
		if(typeof(c_el) != 'undefined' && c_el != undefined && c_el){
			if(c_ell.attr('id') == 'centinel_authenticate_block'){
				exist_el = true;
			}
		}
		
		if (typeof(CentinelAuthenticateController) != "undefined" || exist_el){
			IWD.OPC.backToOpc();
		}
	}
	
};


function toggleContinueButton(){}//dummy

jQuery(document).ready(function(){
	IWD.OPC.Centinel.init();
});
