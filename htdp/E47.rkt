;; The first three lines of this file were inserted by DrRacket. They record metadata
;; about the language level of this file in a form that our tools can easily process.
#reader(lib "htdp-beginner-reader.ss" "lang")((modname E47) (read-case-sensitive #t) (teachpacks ((lib "image.rkt" "teachpack" "2htdp") (lib "abstraction.rkt" "teachpack" "2htdp") (lib "batch-io.rkt" "teachpack" "2htdp") (lib "itunes.rkt" "teachpack" "2htdp") (lib "universe.rkt" "teachpack" "2htdp") (lib "web-io.rkt" "teachpack" "2htdp"))) (htdp-settings #(#t constructor repeating-decimal #f #t none #f ((lib "image.rkt" "teachpack" "2htdp") (lib "abstraction.rkt" "teachpack" "2htdp") (lib "batch-io.rkt" "teachpack" "2htdp") (lib "itunes.rkt" "teachpack" "2htdp") (lib "universe.rkt" "teachpack" "2htdp") (lib "web-io.rkt" "teachpack" "2htdp")) #f)))
;; HtDP 2e 3 How to Design Programs
;; 2.3.7 Virtual Pet Worlds

;; Exercise 47 Pet Guage

(require 2htdp/image)
(require 2htdp/universe)

;; Virtual Pet Simualtion

;; =================
;; Constants:


; graphical constants
(define W-WIDTH 110)
(define W-HEIGHT 30)
(define G-HEIGHT 10)
(define MT (empty-scene W-WIDTH W-HEIGHT))


;; =================
;; Data definitions:

;; CS is a Number
;; interp. the cat's happiness
(define CS1 0)
(define CS2 50)
(define CS3 100)


;; =================
;; Functions:

;; CS -> CS
;; start the world with (main 100)
;;
(define (main cs)
  (big-bang cs                         ; CS
            (on-tick   tock)           ; CS -> CS
            (to-draw   render)         ; CS -> Image
            (on-key    cat-attention)  ; CS KeyEvent -> CS
            ))

;; CS -> CS
;; produce the next CS countdown from 100
;; range [0, 100]
(check-expect (tock 0) 0)
(check-expect (tock 50) 49.9)
(check-expect (tock 100) 99.9)

(define (tock cs)
  (cond [(<= cs 0) 0]
        [(> cs 100) 100]
        [else (- cs 0.1)]))


;; CS -> Image
;; render the cat's happyness guage
(check-expect (render 50) (place-image/align
                           (rectangle 50 G-HEIGHT "solid" "red") 
                           5 10 "left" "bottom"
                           MT))
(define (render cs)
  (place-image/align
   (rectangle cs G-HEIGHT "solid" "red")
   5 10 "left" "bottom"
   MT))

;; CS KeyEvent -> CS
;; handle cat's feeding and petting
;; up arrow pets cat, down arrow feeds cat

(define (cat-attention cs ke)
  (cond [(key=? ke "up") (pet cs)]
        [(key=? ke "down") (feed cs)]
        [else cs]))


;; CS -> CS
;; pet the cat
;;(check-expect (pet 10) 13.3)

(define (pet cs) (* cs (+ 1 1/3)))


;; CS -> CS
;; feed the cat
;;(check-expect (feed 10) 8)

(define (feed cs) (* cs (- 1 1/5)))

;; run
;; (main 100)
;;