<?php
declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use Chess\Color;
use Chess\Pieces\Piece;
use Chess\Position;
use AsciiRender\GraphicSystem;

//TODO Крч, рот ебал, осталось исправить последнее. Если можем срубить фигуру, которая нас заблокировла, то этот ход нужно сохранить


$board = new \Chess\Board();
$manager = new \Chess\Controllers\PieceManager();
$mover = new \Chess\Controllers\PieceMover();
$graphic = new \Chess\GraphicSystem();
$fenParser = new \Chess\Parsers\FENParser();

//$fenParser->setFENPositions("rnbqkbnr/pppppppp/R7/8/8/8/PPPPPPPP/RNBQKBNR", $board);
//$game = true;
//print("Здарова! Это мои новые ебучие шахматы, нажми 'g', чтобы запустить партию \n или нажми 'e', чтобы пойти нахуй");
//$x = readline("Ввод:");
//if ($x === "g") {
//    while ($game) {
//        $graphic->draw($board);
//        $manager->setPiecesDefenseAndMoves($board);
//        $fx = readline("По ширине какую фигуру выбираешь? от 1 до 8:") - 1;
//        $fy = readline("А по высоте?:") -1;
//        $tx = readline("А куда ходим? Ширина:") -1;
//        $ty = readline("А по высоте?:") - 1;
//        $mover->move(new Position($fx, $fy), new Position($tx, $ty), $board);
//    }
//} else {
//    return;
//}

$pawn = new Piece(Color::White, new Position(2, 5), \Chess\PieceName::Pawn);
$board->addPiece($pawn);
//print_r($board->getPieces());

echo $board;
