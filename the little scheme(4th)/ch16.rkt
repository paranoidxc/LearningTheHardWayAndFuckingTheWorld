#lang sicp

(#%require "./tls.rkt")

(define atom?
  (lambda (n)
    (and (not (pair? n)) (not (null? n)))))
     
(define sub1
  (lambda (n)
    (- n 1)))

(define add1
  (lambda (n)
    (+ n 1)))

(define member?
  (lambda (a lat)
    (cond
      [(null? lat) #f]
      [(eq? (car lat) a) #t]
      [else (member? a (cdr lat))])))

(define sweet-tooth
  (lambda (food)
    (cons food
          (cons (quote cake)
                (quote ())))))

(define last (quote angelfood))

(define sweet-toothL
  (lambda (food)
    (set! last food)
    (cons food
          (cons (quote cake)
                (quote ())))))

(define ingredients (quote ()))

(define sweet-toothR
  (lambda (food)
    (set! ingredients (cons food ingredients))
    (cons food
          (cons (quote cake)
                (quote ())))))

;(define deep
;  (lambda (m)
;    (cond
;      [(zero? m) (quote pizza)]
;      [else (cons (deep (sub1 m))
;                        (quote ()))])))


(define deep
  (lambda (m)
    (cond
      [(zero? m) (quote pizza)]
      [else (cons (deepM (sub1 m))
                        (quote ()))])))

(define Ns (quote ()))
(define Rs (quote ()))

(define deepR
  (lambda (n)
    (let ((result (deep n)))      
      (set! Rs (cons result Rs))
      (set! Ns (cons n Ns))
      result)))

(define find
  (lambda (n Ns Rs)
    (letrec
        ((A (lambda (ns rs)
              (cond
                [(null? ns) #f]
                [(= (car ns) n) (car rs)]
                [else
                 (A (cdr ns) (cdr rs))]))))
      (A Ns Rs))))

;(define deepM
;  (let ((Rs (quote ()))
;        (Ns (quote ())))
;    (lambda (n)
;      (if (member? n Ns)
;          (find n Ns Rs)
;          (let ((result (deep n)))
;           (set! Rs (cons result Rs))
;           (set! Ns (cons n Ns))
;           result)))))
    
(define deepM
  (let ((Rs (quote ()))
        (Ns (quote ())))
    (lambda (n)
      (let ((exists (find n Ns Rs)))
        (if (atom? exists)
            (let ((result (deep n)))
              (set! Rs (cons result Rs))
              (set! Ns (cons n Ns))
              result)
            exists)))))

(define length
  (lambda (l)
    (cond
      [(null? l) 0]
      [else (add1 (length (cdr l)))])))



(define length-b
  (let ((h (lambda (l) 0)))
    (set! h
          (lambda (l)
            (cond
              [(null? l) 0]
              [else (add1 (h (cdr l)))])))
    h))