#lang sicp

(define atom?
  (lambda (n)
    (and (not (pair? n)) (not (null? n)))))

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

(define counter 0)
(define set-counter 0)

(define consC
  (let ((N 0))
    (set! counter
          (lambda () N))
    (set! set-counter
          (lambda (x)
            (set! N x)))
    (lambda (x y)
      (set! N (inc N))
      (cons x y))))



(define deep
  (lambda (m)
    (if (zero? m)
        (quote pizza)
        (consC (deep (dec m))
                     (quote ())))))


(define supercounter
  (lambda (f)
    (letrec
        ((S (lambda (n)
              (if (zero? n)
                  (f n)
                  (let ()
                    (f n)
                    (S (dec n)))))))
         (S 5)
      (counter)
      )))

(define deepM
  (let ((Rs (quote ()))
        (Ns (quote ())))            
    (lambda (n)
      (let ((exists (find n Ns Rs)))
        (if (atom? exists)
            (let ((result 
                   (if (zero? n)
                       (quote pizza)
                       (consC (deepM (dec n))
                              (quote ())))))              
              (set! Rs (cons result Rs))
              (set! Ns (cons n Ns))
              result)
            exists)))))

