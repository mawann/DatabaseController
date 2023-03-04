# DatabaseController

Laravel's Database Builder is very powerful. But if you are a:
1. A veteran programmer who is used to writing database queries directly.
2. Want to use MySQL or PostgreSQL.
3. Want easy-to-read source code.

Then this class might be for you.

Sample usage:

```
Class FooController extends DatabaseController {

  public function foo() {
    if $this->exists('select * from customer where id = :id', [':id' => 1]') {
      // Or using place holder style like these...
      $result = $this->fetch('select salary from customer where id = ?', 1);
      $this->execute('update customer set salary = ? where id = ?', [salary + 100, 1]);
      $result = $this->fetchAll('select * from salary');
    }
  }

}
```

The code above works the same as the code below (if we use Database Builder).

```
<?php

use Illuminate\Support\Facades\DB;

class FooController extends Controller {

	function foo() {
		if (DB::table('customer')->where('id', 1)->exists()) {
			$result = DB::table('customer')->select('salary')->where('id', 1)->first();
			DB::table('customer')->update(['salary' => $result->salary + 100])->where('id', 1);
			$result = DB::table('customer')->get();
		}
	}
}
```
