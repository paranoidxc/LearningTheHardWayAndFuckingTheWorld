package logic

import (
	"context"

	"single-project/internal/svc"
	"single-project/internal/types"

	"github.com/zeromicro/go-zero/core/logx"
)

type GetUserFindLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewGetUserFindLogic(ctx context.Context, svcCtx *svc.ServiceContext) *GetUserFindLogic {
	return &GetUserFindLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *GetUserFindLogic) GetUserFind(req *types.UsertByIdReq) (resp *types.UserByIdResp, err error) {
	// todo: add your logic here and delete this line

	return
}
