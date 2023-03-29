## The Rule of Parameters

> (Initial Version)
> Every parameter is a number.
>
> (Final Version)
>
> Every parameter is a tensor.

## The Rule of Rank

> A tensor's rank is the number of left square brackets before its leftmost scalar.

## The Rule of Members and Elements

> Non-empty lists have members and non-scalar tensors have elements.

## The Rule of Uniform Shape

> All elements of a tensor must have the same shape.

## The Law of Rank and Shape

> The rank of a tensor is equal to the length of its shape.

## The Law of Simple Accumulator Passing

> In a simple accumulator passing function definition every recursive function invocation is unwrapped, and the definition has at most one argument that **does not change** ; an argument that ****changes towards a true** **base test; and another that **accumulates** a result.

## The Law of Sum

> For a tensor t with rank r > 0, the rank of (sum t) is r - 1.

## The Law of Revision

> (Initial Version)
>
> new *****θ*** =** *****θ*** *−* (*α ×* rate of change of loss with respect to** ***θ*** ).
>
> (Final Version)
>
> new *****θ*** =** *****θ*** *−* (*α ×* rate of change of loss w.r.t.** ***θ*** ).

## The Rule of Hyperparameters

> Every hyperparameter either is a scalar or has no value.

## The Rule of Data Sets

> In a data set ( *xs* , *ys* )
>
> both  *xs* and *ys* must have the same number of elements.
>
> The elements of *xs* , however, can have a different shape from the elements of *ys* .

## The Rule of** ***θ*

> ***θ*** is a list of parameters that can have different shapes.

## The Rule of Batches

> A batch of indices consists of random indices that are natural numbers smaller than |*xs*| .

## The Law of Batch Sizes

Each revision in stochastic gradient descent uses only a batch of size** ***batch-size* from the data set and the ranks of the tensors in the batch are the same as the ranks of the tensors in the data set.
