package logic

import (
	"context"
	"github.com/zeromicro/go-zero/core/logc"
	"github.com/zeromicro/go-zero/core/logx"
	"single-project/cmd/internal/svc"
	"single-project/cmd/internal/types"
)

type GetAgentListLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewGetAgentListLogic(ctx context.Context, svcCtx *svc.ServiceContext) *GetAgentListLogic {
	return &GetAgentListLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *GetAgentListLogic) GetAgentList(req *types.AgentListReq) (resp *types.AgentListResp, err error) {
	logc.Info(context.Background(), "GetAgentList call")

	l.svcCtx.Rds.SetCtx(l.ctx, "my_go_zero_key", "hello world")

	test(l)
	resp = &types.AgentListResp{
		Id:   "1",
		Name: "Name for req:" + req.Name,
	}

	return
}

func test(l *GetAgentListLogic) {
	l.svcCtx.SysApisModel.FindList(l.ctx)
}
