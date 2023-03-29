#lang racket
(require malt)

(define line-xs (tensor 2.0 1.0 4.0 3.0))
(define line-ys (tensor 1.8 1.2 4.2 3.3))

(define quad-xs (tensor -1.0 0.0 1.0 2.0 3.0))
(define quad-ys (tensor 2.55 2.14 4.35 10.2 18.25))

(define plane-xs
  (tensor
   (tensor 1.0 2.05)
   (tensor 1.0 3.0)
   (tensor 2.0 2.0)
   (tensor 2.0 3.91)
   (tensor 3.0 6.13)
   (tensor 4.0 8.09)
  ))
 
(define plane-ys
  (tensor 13.99 15.99 18.0 22.4 30.2 37.94))

(declare-hyper revs)
(declare-hyper a)
(declare-hyper batch-size)

;(define batch-size 4)


(define sampling-obj
  (λ (expectant xs ys)
    (let ((n (tlen xs)))
      (λ (θ)
        (let ((b (samples n batch-size)))
          ((expectant (trefs xs b)
                      (trefs ys b))
                      θ))))))

(define gradient-descent
  (lambda (obj θ)
    (let ((f (lambda (θ)
               (map (lambda (p g)
                      (- p (* a g)))
                    θ
                    (gradient-of obj θ)))))
      (revise f revs θ))))

(with-hypers
  ((revs 1000)
   (a 0.01)
   (batch-size 4)
  )
  (gradient-descent
    (sampling-obj
     (l2-loss line) line-xs line-ys)
    (list 0.0 0.0)))


(with-hypers
    ((revs 15000)
     (a 0.001)
     (batch-size 4))
  (gradient-descent
   (sampling-obj
    (l2-loss plane) plane-xs plane-ys)
   (list (tensor 0.0 0.0) 0.0)))
