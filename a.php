<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

function meaningfulName() : string
{
    return Faker\Factory::create()->firstName;
}

function randomStatements() : array
{
    $statements = [];
    $c = random_int(1, 2);
    for($i = 0; $i <= $c; $i++) {
        $statements[] = randomStatement();
    }
    return $statements;
}

function randomExpression() : \PhpParser\Node\Expr
{
    // @todo more random expressions..
    return new \PhpParser\Node\Expr\ConstFetch(new \PhpParser\Node\Name('true'));
}

function randomVariableNode() : \PhpParser\Node\Expr\Variable
{
    return new \PhpParser\Node\Expr\Variable(meaningfulName());
}

function randomScalar() : \PhpParser\Node\Scalar
{
    // @todo different scalar values..
    return new \PhpParser\Node\Scalar\LNumber(random_int(1, 100000));
}

function randomStatement() : \PhpParser\Node
{
    switch(random_int(1, 100)) {
        case 1:
            // @todo Don't want to have too much of this or ends up in too much recursion... need to implement more nodes first!
            return new \PhpParser\Node\Stmt\If_(randomExpression(), randomStatements());
        default:
            return new \PhpParser\Node\Expr\Assign(randomVariableNode(), randomScalar());
    }
}

function randomTopLevel() : \PhpParser\Node
{
    switch (random_int(1, 2)) {
        case 1:
            $classBuilder = new \PhpParser\Builder\Class_(meaningfulName());
            $elementCount = random_int(1, 3);
            for ($i = 0; $i <= $elementCount; $i++) {
                switch(random_int(1, 3)) {
                    case 1:
                        $classBuilder->addStmt((new \PhpParser\Builder\Property(meaningfulName()))->getNode());
                        break;
                    case 2:
                        $methodBuilder = new \PhpParser\Builder\Method(meaningfulName());
                        $methodBuilder->addStmts(randomStatements());
                        $classBuilder->addStmt($methodBuilder->getNode());
                        break;
                    case 3: // @todo const
                        break;
                }
            }
            return $classBuilder->getNode();
        case 2:
            $funcBuilder = new \PhpParser\Builder\Function_(meaningfulName());
            $funcBuilder->addStmts(randomStatements());
            return $funcBuilder->getNode();
    }
}

$ast = [];

for ($i = 0; $i <= 4; $i++) {
    $ast[] = randomTopLevel();
}

echo (new \PhpParser\PrettyPrinter\Standard())->prettyPrint($ast);
