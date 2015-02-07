# Lamantin-api
Lamantin Application Program Interface # JS + PHP + PostgreSQL

![Screenshot](https://github.com/VadimuZz/Lamantin-api/blob/master/git-assets/lamantin.jpg)

	
Test call method

		console.log( api.sendRequest("testMethod") );

Test call method with parameters

		console.log( api.sendRequest("testMethod", { id: 1, vars: { foo: "test" } } ) );

Test call method with parameters and callback

		console.log( api.sendRequest("testMethod", { id: 1, vars: { foo: "test" } }, function(data) {
			console.log(data);
		} ) );

![Screenshot](https://github.com/VadimuZz/Lamantin-api/blob/master/git-assets/lamantin-r.png)
