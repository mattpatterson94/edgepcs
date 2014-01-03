$(document).foundation();
// $("header").headroom({
// 	"classes": {
// 		"initial": "animated",
// 		"pinned": "slideInDown",
// 		"unpinned": "slideOutUp"
// 	}
// });


// Aren't I genuis?
$('a[href^="#"]').click(function(){
	var headerHeight = $('header').height() + 30;
	var catCompPosition = $($(this).attr("href")).offset().top;
	var combinedHeight =  catCompPosition - headerHeight;
	$("html, body").animate({scrollTop: combinedHeight}, "slow");
	// return false;
});

// $("a[href='#repairs']").click(function(){
// var catRepairsPosition = jQuery('#repairs').offset().top;
// $("html, body").animate({scrollTop: catRepairsPosition}, "slow");
// return false;
// });

// $("a[href='#contact']").click(function(){
// var catContactPosition = jQuery('#contact').offset().top;
// $("html, body").animate({scrollTop: catContactPosition}, "slow");
// return false;
// });


function getComputers() {
	return $.ajax({
		url: "json/computers.json"
	});
}

function getAddons() {
	return $.ajax({
		url: "json/addons.json"
	});
}

function computerBuild($scope) {
	$scope.currentcomputer = [];
	$scope.quotelist = [];

	$scope.showQuote = false;
	$scope.itemAdded = false;
	$scope.itemRemoved = false;

	$scope.totalIncomplete = false;

	$scope.totalCost = 0;

	$scope.addons = [];

	$scope.pages = 3;
	$scope.currentpage = 1;

	$('#quoteform').parsley();
	
	$scope.getInfo = function(event) {
		var $computers = $("section.our-computers .computers").find("a");
		var $thiscomputer = 0.00;

		// Remove existing active class
		$("section.our-computers .computers a.active").removeClass("active");

		if (event) {
			$thiscomputer = $computers.index($(event.target).parent());
			$(event.target).parent().addClass("active");
		} else {
			$("section.our-computers .computers a:first").addClass("active");
		}

		$.when(getComputers()).done(function(data){
			for (var i = 0; i < data.computers.length; i++) {
				if (i == $thiscomputer) {
					$scope.currentcomputer = data.computers[i];
					$scope.$apply();
				}
			}
		});
	};

	$scope.listAddons = function() {
		$.when(getAddons()).done(function(data){
			$scope.addons = data.addons;
			$scope.$apply();
		});
	};

	$scope.addComputertoQuote = function() {
		$scope.showQuote = true;
		$scope.itemAdded = true;
		$scope.itemRemoved = false;

		var quoteAddition = {"title": $scope.currentcomputer.title, "price": $scope.currentcomputer.price, "qty": 1};
		$scope.quotelist.push(quoteAddition);
	};

	$scope.addAddontoQuote = function(index) {
		$scope.showQuote = true;
		$scope.itemAdded = true;
		$scope.itemRemoved = false;

		var quoteAddition = {"title": $scope.addons[index].title, "price": $scope.addons[index].price, "qty": 1};
		$scope.quotelist.push(quoteAddition);
	};

	$scope.removeItem = function(index) {
		$scope.itemAdded = false;
		$scope.itemRemoved = true;

		$scope.quotelist.splice(index, 1);
	};

	$scope.total = function() {
		var total = 0;
		$scope.totalIncomplete = false;
		angular.forEach($scope.quotelist, function(item) {
			if (!isNaN(item.price)) {
				total += item.qty * item.price;
			} else {
				$scope.totalIncomplete = true;
			}
		});

		return total;
	};

	$scope.decrement = function() {
		if ($scope.currentpage > 1) {
			$scope.itemAdded = false;
			$scope.itemRemoved = false;

			$scope.currentpage--;
		}
	}

	$scope.increment = function() {

		var lessthantotal = $scope.currentpage < $scope.pages;
		var notblank = !($scope.currentpage == 2 && $scope.total() == 0 && !$scope.totalIncomplete);
		var validform = $('#quoteform').parsley('validate');
		console.log(validform);
		if (lessthantotal && notblank && validform) {
			$scope.itemAdded = false;
			$scope.itemRemoved = false;
			$scope.currentpage++;
		}
	}

	$scope.getInfo();
	$scope.listAddons();

	$scope.submit = function() {
		var validform = $('#quoteform').parsley('validate');
		if (validform) {
			alert("YAY!");
		}
	}
}

$("section.our-computers .addons a").click(function(){
	var item = $(this).parent().next();
	var itemtext = item.text().trim();

	var price = item.next();
	var pricetext = price.text().trim();

	console.log(itemtext + ": " + pricetext);

	return false;
});

$('#quoteform').parsley('addListener', {
    onFieldValidate: function ( elem ) {
        if ( !$( elem ).is( ':visible' ) ) {
            return true;
        }
        return false;
    }
});


$(document).ready(function(){
    // add time stamp fields to any verify forms
    var ts = Math.round((new Date()).getTime() / 1000);
    $('form.verify.captcha').append('<input type="text" name="cap" value="" class="notsafe" style="display: none;" /><input type="hidden" name="vf_page_load_stamp" class="notsafe" value="'+ts+'" /><input type="hidden" name="vf_page_send_stamp" class="vf_page_send_stamp notsafe" value="" />');
    $('form.verify').on('submit', function(){
        var ts = Math.round((new Date()).getTime() / 1000);
        $(this).find('input.vf_page_send_stamp').val(ts);
        return verifyform($(this));
    });


	$('#appointment').datetimepicker({
		inline:true,
		onSelect: function(dateText, inst) {
			$('#appointment-time').value = dateText;
		}
	}); 
});