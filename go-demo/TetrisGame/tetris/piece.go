package tetris

import (
	"math"
	"math/rand"
)

type piece struct {
	shape     []vector
	canRotata bool
	color     int
}

func (p *piece) rotateBack() {
	ang := math.Pi / 2 * 3
	p.rotateWithAngle(ang)
}

func (p *piece) rotate() {
	ang := math.Pi / 2
	p.rotateWithAngle(ang)
}

func (p *piece) rotateWithAngle(ang float64) {
	if !p.canRotata {
		return
	}

	cos := int(math.Round(math.Cos(ang)))
	sin := int(math.Round(math.Sin(ang)))

	for i, e := range p.shape {
		ny := e.y*cos - e.x*sin
		nx := e.y*sin - e.x*cos
		p.shape[i] = vector{ny, nx}
	}
}

var pieces = []piece{
	{
		shape:     []vector{{0, 0}},
		color:     0,
		canRotata: false,
	},
	{
		// L shape
		shape:     []vector{{0, -1}, {0, 0}, {0, 1}, {1, 1}},
		color:     1,
		canRotata: true,
	},
	{
		// oposite L shape
		shape:     []vector{{0, -1}, {0, 0}, {0, 1}, {-1, 1}},
		color:     2,
		canRotata: true,
	},
	{
		// I shape
		shape:     []vector{{0, -1}, {0, 0}, {0, 1}, {0, 2}},
		color:     3,
		canRotata: true,
	},
	{
		// o shape
		shape:     []vector{{1, -1}, {1, 0}, {0, -1}, {0, 0}},
		color:     4,
		canRotata: false,
	},
	{
		// + shape
		shape:     []vector{{0, -1}, {0, 0}, {0, 1}, {1, 0}},
		color:     5,
		canRotata: true,
	},
	{
		// z shape
		shape:     []vector{{1, -1}, {1, 0}, {0, 0}, {0, 1}},
		color:     6,
		canRotata: true,
	},
	{
		// s shape
		shape:     []vector{{0, -1}, {0, 0}, {1, 0}, {1, 1}},
		color:     7,
		canRotata: true,
	},
}

func randowPiece() piece {
	idx := rand.Intn(len(pieces)-1) + 1
	pc := pieces[idx]
	return piece{
		shape:     append([]vector(nil), pc.shape...),
		canRotata: pc.canRotata,
		color:     pc.color,
	}
}
