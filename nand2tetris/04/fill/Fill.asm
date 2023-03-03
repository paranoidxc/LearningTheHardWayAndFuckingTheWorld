// This file is part of www.nand2tetris.org
// and the book "The Elements of Computing Systems"
// by Nisan and Schocken, MIT Press.
// File name: projects/04/Fill.asm

// Runs an infinite loop that listens to the keyboard input.
// When a key is pressed (any key), the program blackens the screen,
// i.e. writes "black" in every pixel;
// the screen should remain fully black as long as the key is pressed.
// When no key is pressed, the program clears the screen, i.e. writes
// "white" in every pixel;
// the screen should remain fully clear as long as no key is pressed.

// Put your code here.
(LISTEN_KBD)
        // set screen to var position OR just use one of R0-R15
        @SCREEN
        D=A
        @POSITION
        M=D

        // get keyboard code
        @KBD
        D=M

        @SET_COLOR_BLACK
        D;JNE

        // R3 <- 0  set color to white
        // or use var name color
        @R3
        M=0

        @RENDER_SCREEN
        0;JMP
(END)

(SET_COLOR_BLACK)
        @R3
        M=-1
        @RENDER_SCREEN
        0;JMP
(END)

(RENDER_SCREEN)
        // render pixel color in location
        // load color to D
        @R3
        D=M
        // change position to color
        @POSITION
        A=M
        M=D

        //cal next pixel position
        @POSITION
        MD=M+1
        //check next position is screen
        @24575
        D=A-D
        @RENDER_SCREEN
        D;JGE

        @LISTEN_KBD
        0;JMP
(END)

@END
0;JMP
