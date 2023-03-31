#lang sicp


(define lots
  (lambda (m)
    (cond
      ((zero? m) (quote ()))
      (else (cons (quote egg)
                  (lots (dec m)))))))

(define lenkth
  (lambda (l)
    (cond
      ((null? l) 0)
      (else (inc (lenkth (cdr l)))))))


(define kar
  (lambda (c)
    (c (lambda (a d) a))))