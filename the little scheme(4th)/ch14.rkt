#lang sicp

(define atom?
  (lambda (x)
    (and (not (pair? x)) (not (null? x)))))

(define add1
  (lambda (x) (+ 1 x)))

(define letcc call-with-current-continuation)

(define leftmost
  (lambda (l)
    (cond
      [(atom? (car l)) (car l)]
      [else (leftmost (car l))])))

; (leftmost '(((a) b) (c d)))
; => a
; (leftmost '(((() a) ())))
; => unknow
(define leftmost-b
  (lambda (l)
    (cond
      [(null? l) (quote ())]
      [(atom? (car l)) (car l)]
      [else (leftmost-b (car l))])))

(define leftmost-c
  (lambda (l)
    (cond
      [(null? l) (quote ())]
      [(atom? (car l)) (car l)]
      [else
       (cond
         [(atom? (leftmost-c (car l)))
          (leftmost-c (car l))]
         [else (leftmost-c (cdr l))])])))
; (leftmost-c '(((a) b) (c d)))
; => a
; (leftmost-c '(((a) ()) (c d)))
; => a
; (leftmost-c '((() a) (c d)))
; => a
(define leftmost-d
  (lambda (l)
    (cond
      [(null? l) (quote ())]
      [(atom? (car l)) (car l)]
      [else
       (let
           ((a (leftmost-d (car l))))
         [cond
           [(atom? a) a]
           [else (leftmost-d (cdr l))]])])))


(define depth*
  (lambda (l)
    (cond
      [(null? l) 1]
      [(atom? (car l))
       (depth* (cdr l))]
      [else
       (cond
         [(> (depth* (cdr l))
             (add1 (depth* (car l))))
          (depth* (cdr l))]
         [else
          (add1 (depth* (car l)))])])))

; error depth-b*
(define depth-b*
  (lambda (l)
    (let
        ((a (add1 (depth-b* (car l))))
         (d (depth-b* (cdr l))))
      (cond
        [(null? l) 1]
        [(atom? (car l)) d]
        [else
         (cond
           [(> d a) d]
           [else a])]))))
;
(define depth*-b
  (lambda (l)
    (cond
      [(null? l) 1]
      [(atom? (car l))
       (depth*-b (cdr l))]
      [else
       (let ((a (add1 (depth*-b (car l))))
             (d (depth*-b (cdr l))))
         (cond
           [(> d a) d]
           [else a]))])))
; (depth*-b '(c (b (a b) a) a))

(define depth*-c
  (lambda (l)
    (cond
      [(null? l) 1]
      [(atom? (car l))
       (depth*-c (cdr l))]
      [else
       (let ((a (add1 (depth*-c (car l))))
             (d (depth*-c (cdr l))))
         (if (> d a) d a))])))


(define depth*-d
  (lambda (l)
    (cond
      [(null? l) 1]
      [(atom? (car l))
       (depth*-d (cdr l))]
      [else
       (let ((a (add1 (depth*-d (car l))))
             (d (depth*-d (cdr l))))
         (max a d))])))

(define depth*-f
  (lambda (l)
    (cond
      [(null? l) 1]
      [(atom? (car l))
       (depth*-f (cdr l))]
      [else
       (max (add1 (depth*-f (car l)))
            (depth*-f (cdr l)))])))




(define leftmost-z  
  (lambda (l)
    (letcc
        (lambda (skip)
          (lm l skip)))))

(define lm
  (lambda (l out)
    (cond
      [(null? l) (quote ())]
      [(atom? (car l)) (out (car l))]      
      [else (let ()
              (lm (car l) out)
              (lm (cdr l) out))])))
; (leftmost-z '(((a)) b (c)))
; => a