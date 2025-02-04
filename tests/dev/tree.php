<xmp><?php

use Murdej\Errors;
use Murdej\TreeMaker;

require_once '../../vendor/autoload.php';

error_reporting(E_ALL);
Errors::toException();
try {
    $tree = TreeMaker::make(
        [
            (object)[ 'id' => 1, 'name' => 'A', 'parentId' => null, ],
            (object)[ 'id' => 2, 'name' => 'B', 'parentId' => null, ],
            (object)[ 'id' => 3, 'name' => 'A.a', 'parentId' => 1, ],
            (object)[ 'id' => 4, 'name' => 'A.b', 'parentId' => 1, ],
            (object)[ 'id' => 6, 'name' => 'B.1', 'parentId' => 2, ],
            (object)[ 'id' => 7, 'name' => 'B.1.I', 'parentId' => 6, ],
        ],
        '.parentId',
        '.id'
    );

    // print_r($tree);

    // print_r(TreeMaker::linearize($tree));

    foreach ($tree as $node) {
        echo "Lin " . $node->item->name . "\n";
        print_r(TreeMaker::linearize($node));
    }

} catch (Throwable $exception) {
    print_r($exception);
}
