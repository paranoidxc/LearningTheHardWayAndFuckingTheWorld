#lang sicp

(define atom?
  (lambda (n)
    (and (not (pair? n)) (not (null? n)))))


(define letcc call-with-current-continuation)

(define deep
  (lambda (m)
    (cond
      ((zero? m) (quote pizza))
      (else (cons (deep (dec m))
                  (quote ()))))))



(define toppings '())

(define deepB
  (lambda (m)
    (cond
      ((zero? m)
       (letcc
        (lambda (jump)
          (set! toppings jump)
          (quote pizza))))
      (else (cons (deepB (dec m))
                         (quote ()))))))

(define deep&co
  (lambda (m k)
    (cond
      ((zero? m) (k (quote pizza)))
      (else
       (deep&co (dec m)
                (lambda (x)
                  (k (cons x (quote ())))))))))

(define deep&coB
  (lambda (m k)
    (cond
      ((zero? m)
       (let ()
         (set! toppings k)
         (k (quote pizza))))
      (else
       (deep&coB (dec m)
                 (lambda (x)
                   (k (cons x (quote ())))))))))



(define leave (lambda (x) '()))

(define walk
  (lambda (l)
    (cond
      ((null? l) (quote ()))
      ((atom? (car l))
       (leave (car l)))
      (else
       (let ()
         (walk (car l))
         (walk (cdr l)))))))


(define start-it
  (lambda (l)
    (letcc
     (lambda (here)
       (set! leave here)
       (walk l)))))

(define fill (lambda (x) '()))

(define waddle
  (lambda (l)
    (cond
      ((null? l) (quote ()))
      ((atom? (car l))
       (let ()
         (letcc
          (lambda (rest)
            (set! fill rest)
            (leave (car l))
            (waddle (cdr l))))))
       (else
        (let ()
          (waddle (car l))
          (waddle (cdr l)))))))


(define start-it2
  (lambda (l)
    (letcc
     (lambda (here)
       (set! leave here)
       (waddle l)))))

(start-it2
   '((donuts)
     (cheerios (cheerios (spaghettios)))
     donuts))