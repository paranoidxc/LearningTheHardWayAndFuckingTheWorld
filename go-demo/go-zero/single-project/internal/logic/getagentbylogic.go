package logic

import (
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/logc"
	"single-project/internal/model"
	"strconv"

	"single-project/internal/svc"
	"single-project/internal/types"

	"github.com/zeromicro/go-zero/core/logx"
)

type GetAgentByLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewGetAgentByLogic(ctx context.Context, svcCtx *svc.ServiceContext) *GetAgentByLogic {
	return &GetAgentByLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *GetAgentByLogic) GetAgentBy(req *types.AgentListReq) (resp *types.AgentListResp, err error) {
	// todo: add your logic here and delete this line
	id, _ := strconv.Atoi(req.Name)
	fmt.Printf("%+v", id)
	res, err := l.svcCtx.SysApisModel.FindOne(l.ctx, int64(id))
	if err != nil {
	}
	fmt.Printf("=========%+v\n", res)
	logc.Info(context.Background(), logx.Field("sys apis find 1", res))

	resp = &types.AgentListResp{
		Id:   strconv.Itoa(int(res.Id)),
		Name: "Name for req:" + req.Name,
	}

	dbRes := &model.SysApis{}
	l.svcCtx.GormDb.Find(dbRes, 1)

	fmt.Printf("dbRes =========%+v\n", dbRes)

	return
}
