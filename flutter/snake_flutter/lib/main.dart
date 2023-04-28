import 'dart:async';
import 'dart:math';

import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'dart:core';

import 'package:flutter/services.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: '贪吃蛇',
      theme: ThemeData(
        primarySwatch: Colors.blue,
      ),
      home: const MyHomePage(title: '贪吃蛇'),
    );
  }
}

class MyHomePage extends StatefulWidget {
  const MyHomePage({super.key, required this.title});

  final String title;

  @override
  State<MyHomePage> createState() => _MyHomePageState();
}

const double size = 10;

enum Direction {
  Up,
  Down,
  Left,
  Right,
}

class _MyHomePageState extends State<MyHomePage> {
  Offset ball = Offset(0, 0);
  List<Offset> snakeList = [Offset(50, 0), Offset(60, 0)];
  Direction direction = Direction.Left;

  @override
  void didChangeDependencies() {
    var period = Duration(milliseconds: 200);
    double maxWidth = MediaQuery.of(context).size.width;
    double widthPad = maxWidth % size;
    maxWidth -= widthPad;

    double maxHeight = MediaQuery.of(context).size.height;
    double heightPad = maxHeight % size;
    maxHeight -= heightPad;

    print("DID");

    Timer.periodic(period, (timer) {
      final snakeHead = snakeList[0];
      List<Offset> newStackList = List.generate(snakeList.length, (index) {
        if (index > 0) {
          return snakeList[index - 1];
        } else {
          switch (direction) {
            case Direction.Up:
              return Offset(
                  snakeHead.dx, (snakeHead.dy - size + maxHeight) % maxHeight);
              break;
            case Direction.Down:
              return Offset(snakeHead.dx, (snakeHead.dy + size) % maxHeight);
              break;
            case Direction.Left:
              return Offset(
                  (snakeHead.dx - size + maxWidth) % maxWidth, snakeHead.dy);
              break;
            case Direction.Right:
              return Offset((snakeHead.dx + size) % maxWidth, snakeHead.dy);
              break;
          }
        }
      });

      if (newStackList[0] == ball) {
        newStackList..add(snakeList[snakeList.length -1]);
        setState(() {
          ball = randomPosition(maxWidth.toInt(), maxHeight.toInt());
        });
      }

      setState(() {
        snakeList = newStackList;
      });
    });

    super.didChangeDependencies();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.title),
      ),
      body: RawKeyboardListener(
        focusNode: FocusNode(),
        autofocus: true,
        onKey: (event) {
          if (event.runtimeType == RawKeyDownEvent) {
            Direction newDirection = Direction.Right;
            switch(event.logicalKey.keyLabel) {
              case "Arrow Up":
                newDirection = Direction.Up;
                break;
              case "Arrow Down":
                newDirection = Direction.Down;
                break;
              case "Arrow Left":
                newDirection = Direction.Left;
                break;
              case "Arrow Right":
                newDirection = Direction.Right;
                break;
            }

            setState(() {
              direction = newDirection;
            });
          }
        },
        child: Stack(
          children: snakeList
              .map(
                (snake) => Positioned.fromRect(
                  rect: Rect.fromCenter(
                      center: adjust(snake), width: 10, height: 10),
                  child: Container(
                    margin: const EdgeInsets.all(1),
                    color: Colors.black,
                  ),
                ),
              )
              .toList()
            ..add(
              Positioned.fromRect(
                rect:
                    Rect.fromCenter(center: adjust(ball), width: 10, height: 10),
                child: Container(
                  color: Colors.orange,
                ),
              ),
            ),
        ),
      ),
    );
  }

  Offset adjust(Offset offset) {
    return Offset(offset.dx + size / 2, offset.dy + size / 2);
  }

  Offset randomPosition(int widthRange, int heightRange) {
    var rng = Random();
    double w = rng.nextInt(widthRange).toDouble();
    double widthPad = w % size;
    w -= widthPad;

    double h = rng.nextInt(heightRange).toDouble();
    double heightPad = h % size;
    h -= heightPad ;

    print("x ");
    print(w);

    print("y ");
    print(h);
    return Offset(w, h);
  }
}
