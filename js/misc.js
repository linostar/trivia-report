$(document).ready(function() {
	$("#formReport").bootstrapValidator({
		feedbackIcons: {
			valid: "glyphicon glyphicon-ok",
			invalid: "glyphicon glyphicon-remove",
			validating: "glyphicon glyphicon-refresh"
		},
		fields: {
			txtQuestion: {
				validators: {
					notEmpty: {
						message: "Please add the question where the mistake took place"
					}
				}
			},
			txtCaptcha: {
				validators: {
					notEmpty: {
						message: "Please enter the correct solution of the equation"
					}
				}
			}
		}
	});
});
