//Sortable Column
;(function($){
	$.fn.sortableLF = function(){
		var form = $(this).closest("form");
		$(this).on("click", function() {
			checkFormField(form, "order", $(this).attr("data-order"));
			checkFormField(form, "orderDir", $(this).attr("data-orderDir"));
			form.submit();
		});
	};
}(jQuery));

//Pagination
;(function($){
	$.fn.pagination = function(){
		var form = $(this).closest("form");
		$(this).on("click", function() {
			app = $(this).attr("data-app");
			action = $(this).attr("data-action");
			limit = $(this).attr("data-limit");
			limitStart = $(this).attr("data-limitstart");
			if(app){
				checkFormField(form, "app", app);
			}
			if(action){
				checkFormField(form, "action", action);
			}
			checkFormField(form, "limit", limit);
			checkFormField(form, "limitStart", limitStart);
			form.submit();
		});
	};
}(jQuery));

//Change Submit
;(function($){
	$.fn.changeSubmit = function(){
		$(this).on("change", function() {
			$(this).closest("form").submit();
		});
	};
}(jQuery));

//Form Buttons
;(function($){
	$.fn.formButton = function(){
		$(this).on("click", function() {
			//Data
			element = $(this);
			if(!element.attr("data-selector")){
				$form = $(this).closest("form");
				if(!$form.length){
					$form = $("#mainForm");
				}
			}else{
				$form = $(element.attr("data-selector"));
			}
			app = element.attr("data-app"); 
			action = element.attr("data-action");
			confirmation = element.attr("data-confirmation");
			ajax = element.attr("data-ajax");
			modalId = element.attr("data-modal"); 
			noAjax = element.attr("data-noajax");
			link = element.attr("data-link");
			//Start
			element.removeAttr("prevent-ladda");
			//Confirmation
			if(confirmation){
				if(!confirm(confirmation)){
					element.removeClass("disabled");
					element.disabled = false;
					element.attr("prevent-ladda", "true");
					return false;
				}
			}
			//Modal
			if(modalId){
				$("#" + modalId).on('shown.bs.modal', function (e) {
					Ladda.stopAll();
				});
				$("#" + modalId).modal('show');
				return false;
			}
			//Link
			if(link){
				window.location.href = link;
				return false;
			}
			//No action / non-ajax
			if(!action && noAjax){
				//Check router
				var router = $form.find('input[name=router]').val();
				if(router){
					app = router + "/" + app;
				}
				window.location.href = URL + app + "/" +  action;
				element.removeClass("disabled");
				element.disabled = false;
				//Lada spinners
				Ladda.stopAll();
				return false;
			}
			//Disable element
			if(element){
				if(element.length){
					if(element.hasClass("disabled")){
						return false;
					}else{
						element.addClass("disabled");
						element.disabled = true;
					}
				}
			}
			//App
			if(app){
				checkFormField($form, "app", app);
			}
			//Action
			if(action){
				checkFormField($form, "action", action);
			}
			//Non-ajax
			if(noAjax){
				$form.removeClass("ajax");
			}else{
				$form.addClass("ajax");
			}
			//Submit
			$form.submit();
			//Restore
			if(ajax || external){
				element.removeClass("disabled");
				element.disabled = false;
				//Lada spinners
				Ladda.stopAll();
				$form.find('input[name=app]').val("");
				$form.find('input[name=action]').val("");
			}
			return false;
		});
	};
}(jQuery));

//Auto appends (if needed) hidden field
function checkFormField(formElement, fieldName, fieldValue){
	var field = formElement.find("input[name='" + fieldName + "']");
	if(!field.length){
		$('<input>').attr({
		    type: 'hidden',
		    name: fieldName,
		    value: fieldValue
		}).appendTo(formElement);
	}else{
		field.val(fieldValue);
	}
}

function processMessages(messages, form){
	if(messages.length){
		for(var x=0;x<messages.length;x++) {
			//Field message
			if(messages[x].field){
				field = form.find("select[name=" + messages[x].field + "], input[name=" + messages[x].field + "], textarea[name=" + messages[x].field + "], checkbox[name=" + messages[x].field + "]");
				if(field.length){
					field.parent().parent().addClass("has-" + messages[x].type);
					field.parent().append('<span class="help-block">' + messages[x].message + '</span>');
				}else{
					$("#mensajes-sys").append('<div class="alert alert-' + messages[x].type + '"><button type="button" class="close" data-dismiss="alert">&times;</button>' + messages[x].message + '</div>');
					$('html,body').animate({ scrollTop: 0 }, 'slow');
				}
			//Url redirection
			}else if(messages[x].url){
				$(".alert").remove();
				redirect = true;
				document.location.href = messages[x].url;
			//Message without field
			}else{
				$("#mensajes-sys").append('<div class="alert alert-' + messages[x].type + '"><button type="button" class="close" data-dismiss="alert">&times;</button>' + messages[x].message + '</div>');
				$('html,body').animate({ scrollTop: 0 }, 'slow');
			}
		}
	}
}